<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\SaleItem;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ItemController extends Controller
{
    private function itemListQuery(string $type, bool $includeInactive = false)
    {
        $query = Item::with('category')->where('type', $type);

        if (!$includeInactive) {
            $query->active();
        }

        return $query;
    }

    private function normalizeDecimal(mixed $value, float $default = 0): float
    {
        if ($value === null || $value === '') {
            return $default;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        return $default;
    }

    private function storeItemImages(Request $request): array
    {
        $imagePaths = [];

        // Handle the main item image first (from the new item image field)
        if ($request->hasFile('item_image')) {
            $image = $request->file('item_image');
            if ($image) {
                $imagePaths[] = $image->store('items', 'public');
            }
        } elseif ($request->hasFile('images')) {
            foreach ((array) $request->file('images') as $image) {
                if ($image) {
                    $imagePaths[] = $image->store('items', 'public');
                }
            }
        } elseif ($request->hasFile('image')) {
            $imagePaths[] = $request->file('image')->store('items', 'public');
        }

        return $imagePaths;
    }

    private function normalizeItemImagePaths(?array $paths, ?string $fallbackPath = null): array
    {
        $normalized = array_values(array_filter($paths ?? []));

        if (empty($normalized) && $fallbackPath) {
            $normalized[] = $fallbackPath;
        }

        return $normalized;
    }

    private function deleteStoredImages(array $paths): void
    {
        foreach (array_filter($paths) as $path) {
            Storage::disk('public')->delete($path);
        }
    }

    public function index(Request $request)
    {
        if ($request->has('json')) {
            $includeInactive = $request->boolean('include_inactive');
            $query = $this->itemListQuery('product', $includeInactive);
            if ($request->has('category_id'))
                $query->where('category_id', $request->category_id);
            if ($request->has('uncategorized'))
                $query->whereNull('category_id');
            return response()->json($query->get());
        }

        $products = $this->itemListQuery('product', true)->get();

        if ($products->isEmpty()) {
            return view('items.products', compact('products'));
        }

        return view('items.index', compact('products'));
    }

    public function services(Request $request)
    {
        if ($request->has('json')) {
            return response()->json(
                $this->itemListQuery('service', $request->boolean('include_inactive'))->get()
            );
        }

        $services = $this->itemListQuery('service', true)->get();
        return view('items.services', compact('services'));
    }

    public function create(Request $request)
    {
        $categories = Category::all();
        $units      = [];
        $taxes      = [];
        return view('items.create', compact('categories', 'units', 'taxes'));
    }

    public function store(Request $request)
    {
        $data = $request->isJson() ? $request->json()->all() : $request->all();
        $type = $data['type'] ?? 'product';

        $categoryId = null;
        if (!empty($data['category_id'])) {
            $categoryId = $data['category_id'];
        } elseif (!empty($data['category'])) {
            $cat = Category::where('name', $data['category'])->first();
            $categoryId = $cat?->id;
        }

        $imagePaths = $this->storeItemImages($request);

        $item = Item::create([
            'type'            => $type,
            'name'            => $data['name']           ?? '',
            'category_id'     => $categoryId,
            'unit'            => $data['unit']            ?? '',
            'sale_price'      => $this->normalizeDecimal($data['sale_price'] ?? 0),
            'wholesale_price' => $this->normalizeDecimal($data['wholesale_price'] ?? 0),
            'purchase_price'  => $this->normalizeDecimal($data['purchase_price'] ?? $data['cost_price'] ?? 0),
            'opening_qty'     => $this->normalizeDecimal($data['opening_qty'] ?? 0),
            'item_code'       => $data['item_code']       ?? null,
            'location'        => $data['location']        ?? null,
            'description'     => $data['description']     ?? null,
            'image_path'      => $imagePaths[0] ?? null,
            'image_paths'     => $imagePaths ?: null,
            'min_stock'       => $this->normalizeDecimal($data['min_stock'] ?? 0),
            'is_active'       => array_key_exists('is_active', $data) ? filter_var($data['is_active'], FILTER_VALIDATE_BOOLEAN) : true,
        ]);

        return response()->json([
            'redirect' => $type === 'service' ? route('items.services') : route('items'),
            'item'     => $item,
        ]);
    }

    public function edit(string $id)
    {
        $item       = Item::with('category')->findOrFail($id);
        $categories = Category::all();
        $units      = [];
        $taxes      = [];
        return view('items.edit', compact('item', 'categories', 'units', 'taxes'));
    }

    public function update(Request $request, string $id)
    {
        $item = Item::findOrFail($id);
        $data = $request->isJson() ? $request->json()->all() : $request->all();

        $categoryId = $item->category_id;
        if (!empty($data['category_id'])) {
            $categoryId = $data['category_id'];
        } elseif (!empty($data['category'])) {
            $cat = Category::where('name', $data['category'])->first();
            $categoryId = $cat?->id;
        }

        $existingImagePaths = $this->normalizeItemImagePaths($item->image_paths, $item->image_path);
        $imagePaths = $existingImagePaths;

        if ($request->hasFile('images') || $request->hasFile('image')) {
            $this->deleteStoredImages($existingImagePaths);
            $imagePaths = $this->storeItemImages($request);
        }

        $item->update([
            'name'            => $data['name']            ?? $item->name,
            'category_id'     => $categoryId,
            'unit'            => $data['unit']             ?? $item->unit,
            'sale_price'      => $this->normalizeDecimal($data['sale_price'] ?? $item->sale_price, (float) $item->sale_price),
            'wholesale_price' => $this->normalizeDecimal($data['wholesale_price'] ?? $item->wholesale_price, (float) $item->wholesale_price),
            'purchase_price'  => $this->normalizeDecimal($data['purchase_price'] ?? $data['cost_price'] ?? $item->purchase_price, (float) $item->purchase_price),
            'opening_qty'     => $this->normalizeDecimal($data['opening_qty'] ?? $item->opening_qty, (float) $item->opening_qty),
            'item_code'       => $data['item_code']        ?? $item->item_code,
            'location'        => $data['location']         ?? $item->location,
            'description'     => $data['description']      ?? $item->description,
            'image_path'      => $imagePaths[0] ?? null,
            'image_paths'     => $imagePaths ?: null,
            'min_stock'       => $this->normalizeDecimal($data['min_stock'] ?? $item->min_stock, (float) $item->min_stock),
            'is_active'       => array_key_exists('is_active', $data) ? filter_var($data['is_active'], FILTER_VALIDATE_BOOLEAN) : $item->is_active,
        ]);

        return response()->json(['success' => true, 'item' => $item]);
    }

    public function destroy(string $id)
    {
        $item = Item::find($id);
        if (!$item) {
            return response()->json(['message' => 'Item not found'], 404);
        }
        $item->delete();
        return response()->json(['message' => 'Item deleted successfully']);
    }

    // ── Category ─────────────────────────────────────────────────

    public function importValidItems(Request $request)
    {
        $payload = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.itemName' => ['required', 'string', 'max:255'],
            'items.*.itemCode' => ['nullable', 'string', 'max:255'],
            'items.*.description' => ['nullable', 'string'],
            'items.*.category' => ['nullable', 'string', 'max:255'],
            'items.*.baseUnit' => ['nullable', 'string', 'max:255'],
            'items.*.itemLocation' => ['nullable', 'string', 'max:255'],
            'items.*.salePrice' => ['nullable'],
            'items.*.purchasePrice' => ['nullable'],
            'items.*.wholesalePrice' => ['nullable'],
            'items.*.openingStock' => ['nullable'],
            'items.*.minStock' => ['nullable'],
        ]);

        $rows = $payload['items'];
        $nameCounts = [];
        $codeCounts = [];
        $created = [];
        $errors = [];
        $itemsTableColumns = array_flip(Schema::getColumnListing('items'));
        $itemStocksTableExists = Schema::hasTable('item_stocks');
        $itemStocksColumns = $itemStocksTableExists ? array_flip(Schema::getColumnListing('item_stocks')) : [];

        foreach ($rows as $row) {
            $nameKey = strtolower(trim((string) ($row['itemName'] ?? '')));
            $codeKey = strtolower(trim((string) ($row['itemCode'] ?? '')));

            if ($nameKey !== '') {
                $nameCounts[$nameKey] = ($nameCounts[$nameKey] ?? 0) + 1;
            }

            if ($codeKey !== '') {
                $codeCounts[$codeKey] = ($codeCounts[$codeKey] ?? 0) + 1;
            }
        }

        foreach ($rows as $index => $row) {
            $name = trim((string) ($row['itemName'] ?? ''));
            $itemCode = trim((string) ($row['itemCode'] ?? ''));
            $nameKey = strtolower($name);
            $codeKey = strtolower($itemCode);
            $rowErrors = [];

            if ($name === '') {
                $rowErrors[] = 'Item name is required.';
            }

            if ($nameKey !== '' && ($nameCounts[$nameKey] ?? 0) > 1) {
                $rowErrors[] = 'Duplicate item name found in import file.';
            }

            if ($codeKey !== '' && ($codeCounts[$codeKey] ?? 0) > 1) {
                $rowErrors[] = 'Duplicate item code found in import file.';
            }

            if ($nameKey !== '' && Item::whereRaw('LOWER(TRIM(name)) = ?', [$nameKey])->exists()) {
                $rowErrors[] = 'Item name already exists.';
            }

            if ($codeKey !== '' && Item::whereRaw('LOWER(TRIM(item_code)) = ?', [$codeKey])->exists()) {
                $rowErrors[] = 'Item code already exists.';
            }

            $validator = Validator::make($row, [
                'salePrice' => ['nullable', 'numeric'],
                'purchasePrice' => ['nullable', 'numeric'],
                'wholesalePrice' => ['nullable', 'numeric'],
                'openingStock' => ['nullable', 'numeric'],
                'minStock' => ['nullable', 'numeric'],
            ]);

            if ($validator->fails()) {
                $rowErrors = array_merge($rowErrors, $validator->errors()->all());
            }

            if (!empty($rowErrors)) {
                $errors[] = [
                    'row' => $index + 1,
                    'itemName' => $name,
                    'itemCode' => $itemCode,
                    'error' => implode(' ', array_unique($rowErrors)),
                ];
                continue;
            }

            $categoryId = null;
            $categoryName = trim((string) ($row['category'] ?? ''));
            if ($categoryName !== '') {
                $category = Category::firstOrCreate(['name' => $categoryName]);
                $categoryId = $category->id;
            }

            $salePrice = $this->normalizeDecimal($row['salePrice'] ?? 0);
            $purchasePrice = $this->normalizeDecimal($row['purchasePrice'] ?? 0);
            $wholesalePrice = $this->normalizeDecimal($row['wholesalePrice'] ?? 0);
            $openingStock = $this->normalizeDecimal($row['openingStock'] ?? 0);
            $minStock = $this->normalizeDecimal($row['minStock'] ?? 0);

            $itemData = [
                'type' => 'product',
                'name' => $name,
                'category_id' => $categoryId,
                'unit' => trim((string) ($row['baseUnit'] ?? '')) ?: null,
                'price' => $salePrice,
                'sale_price' => $salePrice,
                'wholesale_price' => $wholesalePrice,
                'purchase_price' => $purchasePrice,
                'opening_qty' => $openingStock,
                'item_code' => $itemCode !== '' ? $itemCode : null,
                'location' => trim((string) ($row['itemLocation'] ?? '')) ?: null,
                'description' => trim((string) ($row['description'] ?? '')) ?: null,
                'min_stock' => $minStock,
                'is_active' => true,
            ];

            $itemData = array_filter(
                $itemData,
                fn ($value, $key) => isset($itemsTableColumns[$key]),
                ARRAY_FILTER_USE_BOTH
            );

            $item = Item::create($itemData);

            if ($itemStocksTableExists) {
                $stockData = [
                    'item_id' => $item->id,
                    'opening_stock' => $openingStock,
                    'current_stock' => $openingStock,
                    'at_price' => $purchasePrice,
                    'as_of_date' => now()->toDateString(),
                    'min_stock_level' => $minStock,
                    'location' => trim((string) ($row['itemLocation'] ?? '')) ?: null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $stockData = array_filter(
                    $stockData,
                    fn ($value, $key) => isset($itemStocksColumns[$key]),
                    ARRAY_FILTER_USE_BOTH
                );

                DB::table('item_stocks')->insert($stockData);
            }

            $created[] = $item;
        }

        return response()->json([
            'success' => empty($errors),
            'message' => count($created) . ' valid items imported successfully.',
            'imported_count' => count($created),
            'error_count' => count($errors),
            'errors' => $errors,
            'items' => $created,
            'redirect' => route('items'),
        ], empty($errors) ? 200 : 422);
    }

    public function category(Request $request)
    {
        $categories = Category::withCount('items')->get();
        $uncategorizedCount = Item::whereNull('category_id')->count();
        return view('items.category', compact('categories', 'uncategorizedCount'));
    }

    public function categoryList()
    {
        return response()->json(Category::all());
    }

    public function exportItemsData()
    {
        $items = Item::with('category')
            ->where('type', 'product')
            ->orderBy('name')
            ->get();

        $rows = $items->map(function (Item $item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'item_code' => $item->item_code,
                'description' => $item->description,
                'category' => $item->category?->name,
                'unit' => $item->unit,
                'sale_price' => $item->sale_price,
                'purchase_price' => $item->purchase_price,
                'opening_qty' => $item->opening_qty,
                'stock_qty' => $item->stock_qty,
                'min_stock' => $item->min_stock,
                'location' => $item->location,
                'created_at' => optional($item->created_at)->format('Y-m-d H:i:s'),
            ];
        });

        return response()->json([
            'success' => true,
            'items' => $rows,
            'count' => $rows->count(),
        ]);
    }

    public function exportItemsDownload()
    {
        $items = Item::with('category')
            ->where('type', 'product')
            ->orderBy('name')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = [
            'Item Name',
            'Item Code',
            'Description',
            'Category',
            'Unit',
            'Sale Price',
            'Purchase Price',
            'Opening Qty',
            'Stock Qty',
            'Min Stock',
            'Location',
            'Created At',
        ];

        foreach ($headers as $index => $header) {
            $sheet->setCellValueByColumnAndRow($index + 1, 1, $header);
        }

        $rowNumber = 2;
        foreach ($items as $item) {
            $sheet->setCellValueByColumnAndRow(1, $rowNumber, $item->name);
            $sheet->setCellValueByColumnAndRow(2, $rowNumber, $item->item_code);
            $sheet->setCellValueByColumnAndRow(3, $rowNumber, $item->description);
            $sheet->setCellValueByColumnAndRow(4, $rowNumber, $item->category?->name);
            $sheet->setCellValueByColumnAndRow(5, $rowNumber, $item->unit);
            $sheet->setCellValueByColumnAndRow(6, $rowNumber, $item->sale_price);
            $sheet->setCellValueByColumnAndRow(7, $rowNumber, $item->purchase_price);
            $sheet->setCellValueByColumnAndRow(8, $rowNumber, $item->opening_qty);
            $sheet->setCellValueByColumnAndRow(9, $rowNumber, $item->stock_qty);
            $sheet->setCellValueByColumnAndRow(10, $rowNumber, $item->min_stock);
            $sheet->setCellValueByColumnAndRow(11, $rowNumber, $item->location);
            $sheet->setCellValueByColumnAndRow(12, $rowNumber, optional($item->created_at)->format('Y-m-d H:i:s'));
            $rowNumber++;
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'export-items-' . now()->format('YmdHis') . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function bulkUpdateData()
    {
        $items = Item::with('category')
            ->where('type', 'product')
            ->orderBy('name')
            ->get();

        $stockMap = [];
        if (Schema::hasTable('item_stocks')) {
            $stockMap = DB::table('item_stocks')
                ->select('item_id', 'opening_stock', 'current_stock', 'at_price', 'as_of_date', 'min_stock_level', 'location')
                ->orderByDesc('id')
                ->get()
                ->unique('item_id')
                ->keyBy('item_id');
        }

        $rows = $items->map(function (Item $item) use ($stockMap) {
            $stock = $stockMap[$item->id] ?? null;

            return [
                'id' => $item->id,
                'name' => $item->name,
                'category_id' => $item->category_id,
                'category_name' => $item->category?->name,
                'item_code' => $item->item_code,
                'description' => $item->description,
                'purchase_price' => $item->purchase_price,
                'sale_price' => $item->sale_price,
                'opening_qty' => $stock->opening_stock ?? $item->opening_qty,
                'current_stock' => $stock->current_stock ?? $item->stock_qty,
                'at_price' => $stock->at_price ?? $item->purchase_price,
                'as_of_date' => isset($stock->as_of_date) ? (string) $stock->as_of_date : optional($item->updated_at)->format('Y-m-d'),
                'min_stock' => $stock->min_stock_level ?? $item->min_stock,
                'location' => $stock->location ?? $item->location,
            ];
        })->values();

        return response()->json([
            'success' => true,
            'items' => $rows,
        ]);
    }

    public function storeCategory(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $cat = Category::create(['name' => $request->name]);
        return response()->json(['category' => $cat]);
    }

    public function updateCategory(Request $request, $id)
    {
        $cat = Category::findOrFail($id);
        $cat->update(['name' => $request->name]);
        return response()->json(['success' => true, 'category' => $cat]);
    }

    public function destroyCategory($id)
    {
        Category::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }

    // ── Units ──────────────────────────────────────────────────────

    public function units()
    {
        return view('items.units');
    }

    public function storeUnit(Request $request)
    {
        return response()->json(['success' => true]);
    }

    public function updateUnit(Request $request, $id)
    {
        return response()->json(['success' => true]);
    }

    public function destroyUnit($id)
    {
        return response()->json(['success' => true]);
    }

    public function show(string $id)
    {
        $item = Item::with('category')->findOrFail($id);

        // If details are requested, return JSON
        if (request()->boolean('details')) {
            return response()->json($item);
        }

        return view('items.show', compact('item'));
    }

    public function transactions(string $id)
    {
        $item = Item::findOrFail($id);

        SaleItem::whereNull('item_id')
            ->where('item_name', $item->name)
            ->update(['item_id' => $item->id]);

        $saleItems = SaleItem::with(['sale.party'])
            ->where(function ($q) use ($id, $item) {
                $q->where('item_id', $id)
                  ->orWhere(function ($q2) use ($item) {
                      $q2->whereNull('item_id')
                         ->where('item_name', $item->name);
                  });
            })
            ->get();

        $typeMap = [
            'invoice'          => 'Sale',
            'sale_return'      => 'Credit Note',
            'proforma'         => 'Proforma Invoice',
            'sale_order'       => 'Sale Order',
            'delivery_challan' => 'Delivery Challan',
            'estimate'         => 'Estimate',
            'pos'              => 'Sale',
        ];

        $transactions = $saleItems->map(function ($si) use ($typeMap) {
            $sale = $si->sale;
            if (!$sale) return null;

            return [
                'id'       => $sale->id,
                'type'     => $typeMap[$sale->type] ?? ucfirst($sale->type),
                'raw_type' => $sale->type,
                'invoice'  => $sale->bill_number ?? $sale->id,
                'name'     => $sale->party?->name ?? 'Walk-in Customer',
                'date'     => $sale->invoice_date
                                ? \Carbon\Carbon::parse($sale->invoice_date)->format('d/m/Y')
                                : \Carbon\Carbon::parse($sale->created_at)->format('d/m/Y'),
                'qty'      => $si->quantity ?? 0,
                'unit'     => $si->unit ?? '',
                'price'    => $si->unit_price ?? 0,
                'status'   => $sale->status ?? 'Unpaid',
                'isAdd'    => !in_array($sale->type, ['sale_return']),
            ];
        })->filter()->values();

        return response()->json($transactions);
    }

    public function adjust(Request $request, string $id)
    {
        $item = Item::findOrFail($id);

        $qty   = floatval($request->input('qty', 0));
        $isAdd = filter_var($request->input('is_add', true), FILTER_VALIDATE_BOOLEAN);

        if ($qty <= 0) {
            return response()->json(['success' => false, 'message' => 'Invalid quantity'], 422);
        }

        if (!$isAdd) {
            $currentStock = floatval($item->stock_qty ?? $item->opening_qty ?? 0);
            if ($qty > $currentStock) {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot reduce. Current stock is only {$currentStock}."
                ], 422);
            }
        }

        if ($isAdd) {
            $item->opening_qty = floatval($item->opening_qty) + $qty;
        } else {
            $item->opening_qty = floatval($item->opening_qty) - $qty;
        }

        $item->save();

        return response()->json([
            'success'     => true,
            'opening_qty' => $item->opening_qty,
            'stock_qty'   => $item->stock_qty,
        ]);
    }

    public function bulkUpdate(Request $request)
    {
        $updates = $request->input('updates', []);

        if (empty($updates)) {
            return response()->json(['success' => false, 'message' => 'No updates provided'], 422);
        }

        try {
            foreach ($updates as $itemId => $fields) {
                $item = Item::find($itemId);
                if (!$item) continue;

                $stockColumns = Schema::hasTable('item_stocks')
                    ? array_flip(Schema::getColumnListing('item_stocks'))
                    : [];

                if (isset($fields['name']) && $fields['name'] !== null && $fields['name'] !== '') {
                    $item->name = $fields['name'];
                }
                if (array_key_exists('category_id', $fields)) {
                    $item->category_id = $fields['category_id'] ?: null;
                }
                if (array_key_exists('item_code', $fields)) {
                    $item->item_code = $fields['item_code'] !== '' ? $fields['item_code'] : null;
                }
                if (array_key_exists('description', $fields) && Schema::hasColumn('items', 'description')) {
                    $item->description = $fields['description'] !== '' ? $fields['description'] : null;
                }
                if (isset($fields['sale_price']) && $fields['sale_price'] !== null && $fields['sale_price'] !== '') {
                    $item->sale_price = floatval($fields['sale_price']);
                }
                if (isset($fields['purchase_price']) && $fields['purchase_price'] !== null && $fields['purchase_price'] !== '') {
                    $item->purchase_price = floatval($fields['purchase_price']);
                }
                if (isset($fields['opening_qty']) && $fields['opening_qty'] !== null && $fields['opening_qty'] !== '') {
                    $item->opening_qty = floatval($fields['opening_qty']);
                }
                if (isset($fields['min_stock']) && $fields['min_stock'] !== null && $fields['min_stock'] !== '') {
                    $item->min_stock = floatval($fields['min_stock']);
                }
                if (array_key_exists('location', $fields)) {
                    $item->location = $fields['location'] !== '' ? $fields['location'] : null;
                }

                $item->save();

                if (!empty($stockColumns)) {
                    $payload = [];

                    if (isset($stockColumns['opening_stock']) && isset($fields['opening_qty']) && $fields['opening_qty'] !== '') {
                        $payload['opening_stock'] = floatval($fields['opening_qty']);
                    }
                    if (isset($stockColumns['current_stock']) && isset($fields['opening_qty']) && $fields['opening_qty'] !== '') {
                        $payload['current_stock'] = floatval($fields['opening_qty']);
                    }
                    if (isset($stockColumns['at_price']) && isset($fields['at_price']) && $fields['at_price'] !== '') {
                        $payload['at_price'] = floatval($fields['at_price']);
                    }
                    if (isset($stockColumns['as_of_date']) && isset($fields['as_of_date']) && $fields['as_of_date'] !== '') {
                        $payload['as_of_date'] = $fields['as_of_date'];
                    }
                    if (isset($stockColumns['min_stock_level']) && isset($fields['min_stock']) && $fields['min_stock'] !== '') {
                        $payload['min_stock_level'] = floatval($fields['min_stock']);
                    }
                    if (isset($stockColumns['location']) && array_key_exists('location', $fields)) {
                        $payload['location'] = $fields['location'] !== '' ? $fields['location'] : null;
                    }

                    if (!empty($payload)) {
                        $stockRecord = DB::table('item_stocks')->where('item_id', $item->id)->orderByDesc('id')->first();

                        if ($stockRecord) {
                            $payload['updated_at'] = now();
                            DB::table('item_stocks')->where('id', $stockRecord->id)->update($payload);
                        } else {
                            DB::table('item_stocks')->insert(array_merge([
                                'item_id' => $item->id,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ], $payload));
                        }
                    }
                }
            }

            return response()->json(['success' => true, 'message' => 'Items updated successfully']);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating items: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkStatus(Request $request)
    {
        $data = $request->validate([
            'item_ids' => ['required', 'array', 'min:1'],
            'item_ids.*' => ['integer', 'exists:items,id'],
            'is_active' => ['required', 'boolean'],
        ]);

        $updated = Item::whereIn('id', $data['item_ids'])->update([
            'is_active' => $data['is_active'],
        ]);

        return response()->json([
            'success' => true,
            'updated' => $updated,
            'message' => $data['is_active']
                ? 'Selected items marked active.'
                : 'Selected items marked inactive.',
        ]);
    }
}
