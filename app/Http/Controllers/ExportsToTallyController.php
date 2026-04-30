<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExportsToTallyController extends Controller
{
    public function index()
    {
        return view('dashboard.utilities.exports-to-tally');
    }

    public function data(Request $request)
    {
        $rows = $this->buildRows($request);

        return response()->json([
            'success' => true,
            'rows' => $rows,
            'count' => count($rows),
        ]);
    }

    public function download(Request $request)
    {
        $rows = $this->buildRows($request);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = [
            'Date',
            'Invoice No.',
            'Party Name',
            'Transaction Type',
            'Payment Type',
            'Amount',
            'Balance',
        ];

        foreach ($headers as $index => $header) {
            $sheet->setCellValueByColumnAndRow($index + 1, 1, $header);
        }

        $rowNumber = 2;
        foreach ($rows as $row) {
            $sheet->setCellValueByColumnAndRow(1, $rowNumber, $row['date'] ?? '');
            $sheet->setCellValueByColumnAndRow(2, $rowNumber, $row['invoice_no'] ?? '');
            $sheet->setCellValueByColumnAndRow(3, $rowNumber, $row['party_name'] ?? '');
            $sheet->setCellValueByColumnAndRow(4, $rowNumber, $row['transaction_type'] ?? '');
            $sheet->setCellValueByColumnAndRow(5, $rowNumber, $row['payment_type'] ?? '');
            $sheet->setCellValueByColumnAndRow(6, $rowNumber, $row['amount'] ?? 0);
            $sheet->setCellValueByColumnAndRow(7, $rowNumber, $row['balance'] ?? 0);
            $rowNumber++;
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'export-to-tally-' . now()->format('YmdHis') . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function push(Request $request)
    {
        $rows = $this->fetchTransactions($request);
        $exportableRows = array_values(array_filter($rows, function (array $row) {
            return $row['normalized_type'] !== 'sale_cancelled';
        }));

        if (empty($exportableRows)) {
            return response()->json([
                'success' => false,
                'message' => 'No exportable rows found. Cancelled sales are skipped for Tally export.',
                'sent_count' => 0,
                'skipped_count' => count($rows),
            ], 422);
        }

        $xml = view('tally.vouchers_xml', ['rows' => $exportableRows])->render();
        $tallyUrl = trim((string) $request->input('tally_url', config('services.tally.url', 'http://localhost:9000')));

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/xml',
                'Accept' => 'application/xml,text/plain,*/*',
            ])->withBody($xml, 'application/xml')->post($tallyUrl);

            $responseBody = (string) $response->body();
            $containsError = stripos($responseBody, '<LINEERROR>') !== false;
            $containsSuccess = stripos($responseBody, '<CREATED>') !== false || stripos($responseBody, 'Created') !== false;

            return response()->json([
                'success' => $response->successful() && !$containsError,
                'message' => $this->buildPushMessage($response->successful(), $containsError, $containsSuccess, count($exportableRows)),
                'sent_count' => count($exportableRows),
                'skipped_count' => max(0, count($rows) - count($exportableRows)),
                'tally_status' => $response->status(),
                'tally_response' => mb_substr($responseBody, 0, 6000),
                'tally_url' => $tallyUrl,
            ], $response->successful() && !$containsError ? 200 : 422);
        } catch (\Throwable $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to connect to Tally. Please check Tally is open and XML server is enabled on port 9000.',
                'sent_count' => 0,
                'skipped_count' => max(0, count($rows) - count($exportableRows)),
                'tally_url' => $tallyUrl,
                'error' => $exception->getMessage(),
            ], 500);
        }
    }

    private function buildRows(Request $request): array
    {
        $rows = $this->fetchTransactions($request);

        return array_map(function (array $row) {
            return [
                'date' => $row['date_display'],
                'invoice_no' => $row['invoice_no'],
                'party_name' => $row['party_name'],
                'transaction_type' => $row['transaction_type'],
                'payment_type' => $row['payment_type'],
                'amount' => $row['amount'],
                'balance' => $row['balance'],
            ];
        }, $rows);
    }

    private function fetchTransactions(Request $request): array
    {
        $startDate = $this->parseDate((string) $request->input('from', $request->query('from')))?->startOfDay();
        $endDate = $this->parseDate((string) $request->input('to', $request->query('to')))?->endOfDay();
        $search = trim((string) $request->input('search', $request->query('search', '')));
        $types = $this->normalizeRequestedTypes($request);

        $rows = [];

        if (in_array('sale', $types, true) || in_array('credit_note', $types, true) || in_array('sale_cancelled', $types, true)) {
            $sales = Sale::with(['party', 'payments'])
                ->when($startDate, function ($query) use ($startDate) {
                    $query->whereDate('invoice_date', '>=', $startDate->toDateString());
                })
                ->when($endDate, function ($query) use ($endDate) {
                    $query->whereDate('invoice_date', '<=', $endDate->toDateString());
                })
                ->when($search !== '', function ($query) use ($search) {
                    $query->where(function ($inner) use ($search) {
                        $inner->where('bill_number', 'like', '%' . $search . '%')
                            ->orWhere('party_name', 'like', '%' . $search . '%')
                            ->orWhereHas('party', function ($partyQuery) use ($search) {
                                $partyQuery->where('name', 'like', '%' . $search . '%');
                            });
                    });
                })
                ->orderByDesc('invoice_date')
                ->orderByDesc('id')
                ->get();

            foreach ($sales as $sale) {
                $normalizedType = $this->normalizeSaleType($sale);

                if (!in_array($normalizedType, $types, true)) {
                    continue;
                }

                $transactionDate = $sale->invoice_date ?? $sale->created_at;
                $paymentType = $sale->payments->first()?->payment_type ?: ($sale->payment_type ?: 'Cash');
                $amount = (float) ($sale->grand_total ?? 0);

                $rows[] = [
                    'sort_date' => (string) (optional($transactionDate)->format('Y-m-d') ?? ''),
                    'date_display' => $this->formatDate($transactionDate),
                    'date_ymd' => optional($transactionDate)->format('Ymd') ?? now()->format('Ymd'),
                    'invoice_no' => $sale->bill_number ?: ('#' . $sale->id),
                    'party_name' => $sale->party?->name ?: ($sale->party_name ?: 'Walk-in Customer'),
                    'transaction_type' => $this->saleLabel($normalizedType),
                    'normalized_type' => $normalizedType,
                    'voucher_type' => $this->voucherTypeFor($normalizedType),
                    'primary_ledger' => $this->primaryLedgerFor($normalizedType),
                    'counter_ledger' => trim((string) $paymentType) !== '' ? trim((string) $paymentType) : 'Cash',
                    'payment_type' => $paymentType,
                    'amount' => $amount,
                    'balance' => (float) ($sale->balance ?? 0),
                    'narration' => 'Exported from Vyapar POS Invoice ' . ($sale->bill_number ?: $sale->id),
                ];
            }
        }

        if (in_array('purchase', $types, true) || in_array('debit_note', $types, true)) {
            $purchases = Purchase::with(['party', 'payments'])
                ->when($startDate, function ($query) use ($startDate) {
                    $query->whereDate('bill_date', '>=', $startDate->toDateString());
                })
                ->when($endDate, function ($query) use ($endDate) {
                    $query->whereDate('bill_date', '<=', $endDate->toDateString());
                })
                ->when($search !== '', function ($query) use ($search) {
                    $query->where(function ($inner) use ($search) {
                        $inner->where('bill_number', 'like', '%' . $search . '%')
                            ->orWhere('party_name', 'like', '%' . $search . '%')
                            ->orWhereHas('party', function ($partyQuery) use ($search) {
                                $partyQuery->where('name', 'like', '%' . $search . '%');
                            });
                    });
                })
                ->orderByDesc('bill_date')
                ->orderByDesc('id')
                ->get();

            foreach ($purchases as $purchase) {
                $normalizedType = $this->normalizePurchaseType($purchase);
                if (!in_array($normalizedType, $types, true)) {
                    continue;
                }

                $transactionDate = $purchase->bill_date ?? $purchase->created_at;
                $paymentType = $purchase->payments->first()?->payment_type ?: 'Cash';
                $amount = (float) ($purchase->grand_total ?? 0);

                $rows[] = [
                    'sort_date' => (string) (optional($transactionDate)->format('Y-m-d') ?? ''),
                    'date_display' => $this->formatDate($transactionDate),
                    'date_ymd' => optional($transactionDate)->format('Ymd') ?? now()->format('Ymd'),
                    'invoice_no' => $purchase->bill_number ?: ('#' . $purchase->id),
                    'party_name' => $purchase->party?->name ?: ($purchase->party_name ?: 'Walk-in Supplier'),
                    'transaction_type' => $normalizedType === 'purchase' ? 'Purchase' : 'Debit Note',
                    'normalized_type' => $normalizedType,
                    'voucher_type' => $this->voucherTypeFor($normalizedType),
                    'primary_ledger' => $this->primaryLedgerFor($normalizedType),
                    'counter_ledger' => trim((string) $paymentType) !== '' ? trim((string) $paymentType) : 'Cash',
                    'payment_type' => $paymentType,
                    'amount' => $amount,
                    'balance' => (float) ($purchase->balance ?? 0),
                    'narration' => 'Exported from Vyapar POS Bill ' . ($purchase->bill_number ?: $purchase->id),
                ];
            }
        }

        usort($rows, function (array $a, array $b) {
            return strcmp((string) ($b['sort_date'] ?? ''), (string) ($a['sort_date'] ?? ''));
        });

        return $rows;
    }

    private function parseDate(?string $value): ?Carbon
    {
        if (!$value) {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable $exception) {
            return null;
        }
    }

    private function formatDate($value): string
    {
        if (!$value) {
            return '-';
        }

        try {
            return Carbon::parse($value)->format('d/m/Y');
        } catch (\Throwable $exception) {
            return '-';
        }
    }

    private function normalizeSaleType(Sale $sale): string
    {
        if ($sale->type === 'sale_return') {
            return 'credit_note';
        }

        if (strtolower((string) $sale->status) === 'cancelled') {
            return 'sale_cancelled';
        }

        return 'sale';
    }

    private function normalizePurchaseType(Purchase $purchase): string
    {
        if ($purchase->type === 'purchase_return') {
            return 'debit_note';
        }

        return 'purchase';
    }

    private function saleLabel(string $type): string
    {
        return match ($type) {
            'credit_note' => 'Credit Note',
            'sale_cancelled' => 'Sale[Cancelled]',
            default => 'Sale',
        };
    }

    private function voucherTypeFor(string $type): string
    {
        return match ($type) {
            'credit_note' => 'Credit Note',
            'purchase' => 'Purchase',
            'debit_note' => 'Debit Note',
            default => 'Sales',
        };
    }

    private function primaryLedgerFor(string $type): string
    {
        return match ($type) {
            'credit_note' => 'Sales Return',
            'purchase' => 'Purchase',
            'debit_note' => 'Purchase Return',
            default => 'Sales',
        };
    }

    private function buildPushMessage(bool $httpOk, bool $containsError, bool $containsSuccess, int $sentCount): string
    {
        if (!$httpOk) {
            return 'Tally server returned an error response.';
        }

        if ($containsError) {
            return 'Tally responded with line errors. Please check ledger/party names in Tally.';
        }

        if ($containsSuccess) {
            return "Export completed. {$sentCount} voucher(s) pushed to Tally.";
        }

        return "Request sent to Tally for {$sentCount} voucher(s). Please verify entries in Tally.";
    }

    private function normalizeRequestedTypes(Request $request): array
    {
        $allowedTypes = ['sale', 'credit_note', 'purchase', 'debit_note', 'sale_cancelled'];

        $types = $request->query('types');
        if (is_string($types) && $types !== '') {
            $types = explode(',', $types);
        }

        if (!is_array($types)) {
            return $allowedTypes;
        }

        $normalized = array_values(array_intersect($allowedTypes, array_map(function ($value) {
            return strtolower(trim((string) $value));
        }, $types)));

        return !empty($normalized) ? $normalized : $allowedTypes;
    }
}
