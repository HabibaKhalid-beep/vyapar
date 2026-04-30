<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $invoice->id }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background:#eef3f9; }
        .sheet { max-width: 860px; margin: 30px auto; background:#fff; border:1px solid #dce5ef; border-radius:20px; padding:30px; box-shadow:0 18px 30px rgba(15,23,42,.08); }
        .invoice-title { font-size: 34px; font-weight: 800; color:#16355d; }
        .simple-table td { padding: 12px 14px; border:1px solid #e2e8f0; }
        .label { color:#64748b; font-size:12px; text-transform:uppercase; font-weight:700; letter-spacing:.08em; }
        .value { font-size:18px; font-weight:700; color:#14253b; }
        @media print {
            .no-print { display:none !important; }
            body { background:#fff; }
            .sheet { box-shadow:none; border:none; margin:0; max-width:none; }
        }
    </style>
</head>
<body>
    <div class="sheet">
        <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
                <div class="invoice-title">Invoice / Parchi</div>
                <div class="text-secondary">Invoice No: {{ $invoice->id }}</div>
            </div>
            <div class="text-end no-print">
                <button class="btn btn-outline-secondary" onclick="window.print()">Print</button>
                @php
                    $message = "Invoice #{$invoice->id}\nParty: " . ($invoice->party?->name ?? '-') . "\nCity: " . ($invoice->party?->city ?? '-') . "\nTadad: {$invoice->tadad}\nTotal Wazan: " . number_format((float) $invoice->total_wazan, 2) . "\nSafi Wazan: " . number_format((float) $invoice->safi_wazan, 2) . "\nRate: " . number_format((float) $invoice->rate, 2) . "\nAmount: " . number_format((float) $invoice->amount, 2);
                @endphp
                <a class="btn btn-success" target="_blank" href="https://wa.me/?text={{ urlencode($message) }}">WhatsApp Share</a>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="label">Party Name</div>
                <div class="value">{{ $invoice->party?->name ?? '-' }}</div>
            </div>
            <div class="col-md-6">
                <div class="label">City</div>
                <div class="value">{{ $invoice->party?->city ?? '-' }}</div>
            </div>
        </div>

        <table class="table simple-table">
            <tbody>
                <tr>
                    <td>
                        <div class="label">Tadad</div>
                        <div class="value">{{ $invoice->tadad }}</div>
                    </td>
                    <td>
                        <div class="label">Total Wazan</div>
                        <div class="value">{{ number_format((float) $invoice->total_wazan, 2) }}</div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="label">Safi Wazan</div>
                        <div class="value">{{ number_format((float) $invoice->safi_wazan, 2) }}</div>
                    </td>
                    <td>
                        <div class="label">Rate</div>
                        <div class="value">{{ number_format((float) $invoice->rate, 2) }}</div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div class="label">Amount</div>
                        <div class="value">{{ number_format((float) $invoice->amount, 2) }}</div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>
