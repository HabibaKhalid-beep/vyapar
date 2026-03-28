<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use Illuminate\Http\Request;

class ItemController extends Controller
{
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

        // Resolve category_id
        $categoryId = null;
        if (!empty($data['category_id'])) {
            $categoryId = $data['category_id'];
        } elseif (!empty($data['category'])) {
            $cat = Category::where('name', $data['category'])->first();
            $categoryId = $cat?->id;
        }

        $item = Item::create([
            'type'            => $type,
            'name'            => $data['name']           ?? '',
            'category_id'     => $categoryId,
            'unit'            => $data['unit']            ?? '',
            'sale_price'      => $data['sale_price']      ?? 0,
            'wholesale_price' => $data['wholesale_price'] ?? 0,
            'purchase_price'  => $data['purchase_price']  ?? $data['cost_price'] ?? 0,
            'opening_qty'     => $data['opening_qty']     ?? 0,
            'item_code'       => $data['item_code']       ?? null,
            'location'        => $data['location']        ?? null,
            'min_stock'       => $data['min_stock']       ?? 0,
        ]);

        if ($request->isJson()) {
            return response()->json([
                'redirect' => $type === 'service' ? route('items.services') : route('items'),
                'item'     => $item,
            ]);
        }

        return $type === 'service'
            ? redirect()->route('items.services')
            : redirect()->route('items');
    }

    public function edit(string $id)
{
    $item       = Item::findOrFail($id);
    $categories = Category::all();
    $units      = [];
    $taxes      = [];
    return view('items.edit', compact('item', 'categories', 'units', 'taxes'));
    //                 ^^^^^ CHANGE THIS
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

        $item->update([
            'name'            => $data['name']            ?? $item->name,
            'category_id'     => $categoryId,
            'unit'            => $data['unit']             ?? $item->unit,
            'sale_price'      => $data['sale_price']       ?? $item->sale_price,
            'wholesale_price' => $data['wholesale_price']  ?? $item->wholesale_price,
            'purchase_price'  => $data['purchase_price']   ?? $data['cost_price'] ?? $item->purchase_price,
            'opening_qty'     => $data['opening_qty']      ?? $item->opening_qty,
            'item_code'       => $data['item_code']        ?? $item->item_code,
            'location'        => $data['location']         ?? $item->location,
            'min_stock'       => $data['min_stock']        ?? $item->min_stock,
        ]);

        if ($request->isJson()) {
            return response()->json(['success' => true, 'item' => $item]);
        }

        return redirect()->route('items');
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

    public function category()
    {
        $categories = Category::withCount('items')->get();
        return view('items.category', compact('categories'));
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
}