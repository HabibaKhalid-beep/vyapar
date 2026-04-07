<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\SaleItem;
use App\Models\Sale;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
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

        if ($request->hasFile('images')) {
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
            $query = Item::with('category')->where('type', 'product');
            if ($request->has('category_id'))
                $query->where('category_id', $request->category_id);
            if ($request->has('uncategorized'))
                $query->whereNull('category_id');
            return response()->json($query->get());
        }

        $products = Item::with('category')->where('type', 'product')->get();

        if ($products->isEmpty()) {
            return view('items.products', compact('products'));
        }

        return view('items.index', compact('products'));
    }

    public function services(Request $request)
    {
        if ($request->has('json')) {
            return response()->json(
                Item::with('category')->where('type', 'service')->get()
            );
        }

        $services = Item::with('category')->where('type', 'service')->get();
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

                if (isset($fields['name']) && $fields['name'] !== null && $fields['name'] !== '') {
                    $item->name = $fields['name'];
                }
                if (isset($fields['sale_price']) && $fields['sale_price'] !== null && $fields['sale_price'] !== '') {
                    $item->sale_price = floatval($fields['sale_price']);
                }
                if (isset($fields['purchase_price']) && $fields['purchase_price'] !== null && $fields['purchase_price'] !== '') {
                    $item->purchase_price = floatval($fields['purchase_price']);
                }

                $item->save();
            }

            return response()->json(['success' => true, 'message' => 'Items updated successfully']);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating items: ' . $e->getMessage()
            ], 500);
        }
    }
}