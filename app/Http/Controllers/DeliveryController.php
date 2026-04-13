<?php

namespace App\Http\Controllers;

use App\Models\ChallanDetail;
use App\Models\Broker;
use App\Models\BankAccount;
use App\Models\Item;
use App\Models\Party;
use App\Models\Sale;
use App\Models\User;
use App\Models\Warehouse;
use App\Notifications\DeliveryChallanAssignedNotification;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DeliveryController extends Controller
{
    public function deliveryChallan()
    {
        $challans = Sale::where('type', 'delivery_challan')
            ->with(['items', 'challanDetail', 'party'])
            ->orderByDesc('created_at')
            ->get();

        $convertedInvoices = Sale::where('type', 'invoice')
            ->whereNotNull('reference_id')
            ->get()
            ->keyBy('reference_id');

        return view('dashboard.delivery.delivery-challan', compact('challans', 'convertedInvoices'));
    }

    public function createChallan()
    {
        return $this->renderChallanForm();
    }

    public function edit(Sale $sale)
    {
        abort_unless($sale->type === 'delivery_challan', 404);

        $sale->load(['items', 'challanDetail']);

        return $this->renderChallanForm($sale);
    }

    public function duplicate(Sale $sale)
    {
        abort_unless($sale->type === 'delivery_challan', 404);

        $sale->load(['items', 'challanDetail']);

        return $this->renderChallanForm(null, $sale);
    }

    private function renderChallanForm(?Sale $challan = null, ?Sale $duplicateChallan = null)
    {
        $items = Item::active()->orderBy('name')->get();
        $parties = Party::orderBy('name')->get();
        $brokers = Broker::orderBy('name')->get();
        $bankAccounts = BankAccount::orderBy('bank_name')->get();
        $users = User::orderBy('name')->get(['id', 'name', 'email']);
        $warehouses = Warehouse::with('responsibleUser:id,name,email')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        $nextSaleId = (Sale::max('id') ?? 0) + 1;
        $nextInvoiceNumber = 'DC-' . str_pad((string) $nextSaleId, 4, '0', STR_PAD_LEFT);

        return view('dashboard.delivery.create-challan', compact('items', 'parties', 'brokers', 'bankAccounts', 'users', 'warehouses', 'nextInvoiceNumber', 'challan', 'duplicateChallan'));
    }

    public function store(Request $request)
    {
        $data = $this->validateChallanRequest($request);

        $sale = DB::transaction(function () use ($request, $data) {
            [$imagePaths, $primaryImagePath] = $this->storeChallanImages($request);

            $sale = Sale::create($this->buildSalePayload($data, $primaryImagePath, $imagePaths));

            foreach ($data['items'] as $item) {
                $sale->items()->create($this->buildItemPayload($item));
            }

            $challanDetail = ChallanDetail::create($this->buildChallanDetailPayload($data, $sale));

            $this->notifyResponsibleUser($challanDetail, $sale);

            return $sale;
        });

        return response()->json([
            'success' => true,
            'sale_id' => $sale->id,
            'bill_number' => $sale->bill_number,
            'redirect_url' => route('invoice', ['sale_id' => $sale->id, 'doc' => 'delivery_challan']),
            'share_url' => route('invoice', ['sale_id' => $sale->id, 'doc' => 'delivery_challan']),
        ]);
    }

    public function update(Request $request, Sale $sale)
    {
        abort_unless($sale->type === 'delivery_challan', 404);

        $data = $this->validateChallanRequest($request);

        DB::transaction(function () use ($request, $data, $sale) {
            $existingImagePaths = collect($sale->image_paths ?? [])
                ->filter()
                ->values()
                ->all();

            if (empty($existingImagePaths) && $sale->image_path) {
                $existingImagePaths = [$sale->image_path];
            }

            [$imagePaths, $primaryImagePath] = $this->storeChallanImages($request, $data['existing_image_paths'] ?? [], $existingImagePaths);

            $sale->update($this->buildSalePayload($data, $primaryImagePath, $imagePaths));
            $sale->items()->delete();

            foreach ($data['items'] as $item) {
                $sale->items()->create($this->buildItemPayload($item));
            }

            $challanDetail = $sale->challanDetail()->updateOrCreate(
                ['sale_id' => $sale->id],
                $this->buildChallanDetailPayload($data, $sale)
            );

            $this->notifyResponsibleUser($challanDetail, $sale);
        });

        return response()->json([
            'success' => true,
            'sale_id' => $sale->id,
            'bill_number' => $sale->bill_number,
            'redirect_url' => route('delivery-challan'),
            'share_url' => route('invoice', ['sale_id' => $sale->id, 'doc' => 'delivery_challan']),
        ]);
    }

    public function destroy(Sale $sale)
    {
        abort_unless($sale->type === 'delivery_challan', 404);

        foreach (array_filter($sale->image_paths ?? [$sale->image_path]) as $imagePath) {
            Storage::disk('public')->delete($imagePath);
        }

        $sale->challanDetail()?->delete();
        $sale->items()->delete();
        $sale->payments()->delete();
        $sale->delete();

        return response()->json([
            'success' => true,
            'message' => 'Delivery challan deleted successfully.',
        ]);
    }

    public function preview(Sale $sale)
    {
        abort_unless($sale->type === 'delivery_challan', 404);
        $sale->load(['items', 'challanDetail']);

        return view('dashboard.delivery.challan-preview', compact('sale'));
    }

    public function print(Sale $sale)
    {
        abort_unless($sale->type === 'delivery_challan', 404);
        $sale->load(['items', 'challanDetail']);

        return view('dashboard.delivery.challan-preview', ['sale' => $sale, 'autoPrint' => true]);
    }

    public function pdf(Sale $sale)
    {
        abort_unless($sale->type === 'delivery_challan', 404);
        $sale->load(['items', 'challanDetail']);

        return view('dashboard.delivery.challan-preview', ['sale' => $sale, 'pdfMode' => true]);
    }

    private function validateChallanRequest(Request $request): array
    {
        if (is_string($request->input('items'))) {
            $decodedItems = json_decode($request->input('items'), true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $request->merge([
                    'items' => $decodedItems,
                ]);
            }
        }

        return $request->validate([
            'party_id' => 'nullable|exists:parties,id',
            'broker_id' => 'nullable|exists:brokers,id',
            'broker_name' => 'nullable|string|max:255',
            'broker_phone' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:50',
            'billing_address' => 'nullable|string|max:1000',
            'shipping_address' => 'nullable|string|max:1000',
            'bill_number' => 'required|string|max:100',
            'invoice_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'warehouse_name' => 'nullable|string|max:255',
            'warehouse_phone' => 'nullable|string|max:50',
            'warehouse_handler_name' => 'nullable|string|max:255',
            'warehouse_handler_phone' => 'nullable|string|max:50',
            'responsible_user_id' => 'nullable|exists:users,id',
            'vehicle_number' => 'nullable|string|max:100',
            'destination' => 'nullable|string|max:255',
            'delivery_expenses' => 'nullable|numeric|min:0',
            'total_qty' => 'nullable|integer|min:0',
            'total_amount' => 'nullable|numeric|min:0',
            'discount_pct' => 'nullable|numeric|min:0',
            'discount_rs' => 'nullable|numeric|min:0',
            'tax_pct' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'round_off' => 'nullable|numeric',
            'grand_total' => 'nullable|numeric|min:0',
            'status' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'image_path' => 'nullable|string|max:255',
            'existing_image_paths' => 'nullable|array',
            'existing_image_paths.*' => 'nullable|string|max:255',
            'images' => 'nullable|array',
            'images.*' => 'nullable|file|image',
            'document_path' => 'nullable|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'nullable|string|max:255',
            'items.*.item_category' => 'nullable|string|max:255',
            'items.*.item_code' => 'nullable|string|max:255',
            'items.*.item_description' => 'nullable|string',
            'items.*.quantity' => 'nullable|integer|min:0',
            'items.*.unit' => 'nullable|string|max:50',
            'items.*.unit_price' => 'nullable|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'items.*.amount' => 'nullable|numeric|min:0',
        ]);
    }

    private function buildSalePayload(array $data, ?string $primaryImagePath = null, array $imagePaths = []): array
    {
        return [
            'type' => 'delivery_challan',
            'party_id' => $data['party_id'] ?? null,
            'broker_id' => $data['broker_id'] ?? null,
            'phone' => $data['phone'] ?? null,
            'billing_address' => $data['billing_address'] ?? null,
            'shipping_address' => $data['shipping_address'] ?? null,
            'bill_number' => $data['bill_number'],
            'invoice_date' => $data['invoice_date'] ?? now()->toDateString(),
            'due_date' => $data['due_date'] ?? ($data['invoice_date'] ?? now()->toDateString()),
            'total_qty' => $data['total_qty'] ?? 0,
            'total_amount' => $data['total_amount'] ?? 0,
            'discount_pct' => $data['discount_pct'] ?? 0,
            'discount_rs' => $data['discount_rs'] ?? 0,
            'tax_pct' => $data['tax_pct'] ?? 0,
            'tax_amount' => $data['tax_amount'] ?? 0,
            'round_off' => $data['round_off'] ?? 0,
            'grand_total' => $data['grand_total'] ?? 0,
            'received_amount' => 0,
            'balance' => $data['grand_total'] ?? 0,
            'status' => $data['status'] ?? 'open',
            'description' => $data['description'] ?? null,
            'image_path' => $primaryImagePath ?? $data['image_path'] ?? null,
            'image_paths' => !empty($imagePaths) ? array_values($imagePaths) : null,
            'document_path' => $data['document_path'] ?? null,
        ];
    }

    private function buildChallanDetailPayload(array $data, Sale $sale): array
    {
        return [
            'sale_id' => $sale->id,
            'challan_number' => $sale->bill_number,
            'invoice_date' => $sale->invoice_date,
            'due_date' => $sale->due_date,
            'broker_name' => $data['broker_name'] ?? null,
            'broker_phone' => $data['broker_phone'] ?? null,
            'warehouse_id' => $data['warehouse_id'] ?? null,
            'warehouse_name' => $data['warehouse_name'] ?? null,
            'warehouse_phone' => $data['warehouse_phone'] ?? null,
            'warehouse_handler_name' => $data['warehouse_handler_name'] ?? null,
            'warehouse_handler_phone' => $data['warehouse_handler_phone'] ?? null,
            'responsible_user_id' => $data['responsible_user_id'] ?? null,
            'vehicle_number' => $data['vehicle_number'] ?? null,
            'destination' => $data['destination'] ?? null,
            'delivery_expenses' => $data['delivery_expenses'] ?? 0,
        ];
    }

    private function buildItemPayload(array $item): array
    {
        return [
            'item_name' => $item['item_name'] ?? null,
            'item_category' => $item['item_category'] ?? null,
            'item_code' => $item['item_code'] ?? null,
            'item_description' => $item['item_description'] ?? null,
            'quantity' => $item['quantity'] ?? 0,
            'unit' => $item['unit'] ?? null,
            'unit_price' => $item['unit_price'] ?? 0,
            'discount' => $item['discount'] ?? 0,
            'amount' => $item['amount'] ?? 0,
        ];
    }

    private function storeChallanImages(Request $request, array $existingPaths = [], array $originalPaths = []): array
    {
        $paths = collect($existingPaths)->filter()->values()->all();

        if ($request->hasFile('images')) {
            foreach ($request->file('images', []) as $image) {
                if ($image instanceof UploadedFile) {
                    $paths[] = $image->store('delivery-challans', 'public');
                }
            }
        }

        $paths = array_values(array_unique(array_filter($paths)));

        $deletedPaths = array_diff(array_filter($originalPaths), $paths);
        foreach ($deletedPaths as $deletedPath) {
            Storage::disk('public')->delete($deletedPath);
        }

        return [$paths, $paths[0] ?? null];
    }

    private function notifyResponsibleUser(ChallanDetail $challanDetail, Sale $sale): void
    {
        $responsibleUser = $challanDetail->responsibleUser;

        if (!$responsibleUser) {
            return;
        }

        $sale->loadMissing(['party', 'challanDetail']);
        $responsibleUser->notify(new DeliveryChallanAssignedNotification($sale));
        $challanDetail->forceFill([
            'notification_sent_at' => now(),
        ])->save();
    }
}
