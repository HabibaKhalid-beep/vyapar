<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExpenseCategory;
use App\Models\ExpenseItem;
use App\Models\Expense;
use Illuminate\Support\Facades\Auth;

class ExpenseCreateController extends Controller
{
    // ─── MAIN VIEW ───────────────────────────────────────────────
    public function expense()
    {
        $userId = Auth::id();

        $categoriesRaw = ExpenseCategory::where('user_id', $userId)
            ->with(['expenses' => function($q) {
                $q->orderBy('expense_date', 'desc');
            }])
            ->orderBy('name')
            ->get();

        $categories = $categoriesRaw->map(function($cat) {
            return [
                'id'      => $cat->id,
                'name'    => $cat->name,
                'type'    => $cat->type,
                'amount'  => $cat->expenses->sum('total_amount'),
                'entries' => $cat->expenses->map(function($e) {
                    return [
                        'id'          => $e->id,
                        'date'        => $e->expense_date,
                        'expNo'       => $e->expense_no ?? '',
                        'party'       => $e->party ?? '',
                        'paymentType' => $e->payment_type,
                        'amount'      => $e->total_amount,
                        'balance'     => $e->balance,
                    ];
                })->values(),
            ];
        })->values();

        $expenseItems = ExpenseItem::where('user_id', $userId)
            ->orderBy('name')
            ->get()
            ->map(fn($it) => ['id' => $it->id, 'name' => $it->name, 'price' => $it->price])
            ->values();

        return view('dashboard.expense.expense', compact('categories', 'expenseItems'));
    }

    // ─── EXPENSE CATEGORY CRUD ───────────────────────────────────
    public function storeCategory(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255', 'type' => 'required|string']);
        $cat = ExpenseCategory::create([
            'user_id' => Auth::id(),
            'name'    => $request->name,
            'type'    => $request->type,
        ]);
        return response()->json(['success' => true, 'category' => [
            'id' => $cat->id, 'name' => $cat->name, 'type' => $cat->type, 'amount' => 0, 'entries' => []
        ]]);
    }

   public function updateCategory(Request $request, $id)
{
    $category = ExpenseCategory::where('user_id', auth()->id())
                    ->findOrFail($id);

    $category->update([
        'name' => $request->name,
        'type' => $request->type,
    ]);

    return response()->json(['success' => true, 'category' => $category]);
}

    public function destroyCategory($id)
    {
        $cat = ExpenseCategory::where('user_id', Auth::id())->findOrFail($id);
        if ($cat->expenses()->count() > 0) {
            return response()->json(['success' => false, 'message' => 'Cannot delete: category has transactions.'], 422);
        }
        $cat->delete();
        return response()->json(['success' => true]);
    }

    // ─── EXPENSE ITEM CRUD ───────────────────────────────────────
    public function storeItem(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $item = ExpenseItem::create([
            'user_id' => Auth::id(),
            'name'    => $request->name,
            'price'   => $request->price ?? 0,
        ]);
        return response()->json(['success' => true, 'item' => [
            'id' => $item->id, 'name' => $item->name, 'price' => $item->price
        ]]);
    }

    public function updateItem(Request $request, $id)
    {
        $item = ExpenseItem::where('user_id', Auth::id())->findOrFail($id);
        $item->update([
            'name'  => $request->name ?? $item->name,
            'price' => $request->price ?? $item->price,
        ]);
        return response()->json(['success' => true, 'item' => [
            'id' => $item->id, 'name' => $item->name, 'price' => $item->price
        ]]);
    }

    public function destroyItem($id)
    {
        $item = ExpenseItem::where('user_id', Auth::id())->findOrFail($id);
        $item->delete();
        return response()->json(['success' => true]);
    }

    // ─── EXPENSE CRUD ─────────────────────────────────────────────
    public function storeExpense(Request $request)
    {
        $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'expense_date'        => 'required|date',
            'total_amount'        => 'required|numeric|min:0',
            'payment_type'        => 'required|string',
        ]);

        $expense = Expense::create([
            'user_id'             => Auth::id(),
            'expense_category_id' => $request->expense_category_id,
            'expense_no'          => $request->expense_no,
            'expense_date'        => $request->expense_date,
            'total_amount'        => $request->total_amount,
            'payment_type'        => $request->payment_type,
            'reference_no'        => $request->reference_no,
            'party'               => $request->party ?? '',
            'balance'             => 0,
        ]);

        return response()->json(['success' => true, 'expense' => [
            'id'          => $expense->id,
            'date'        => $expense->expense_date,
            'expNo'       => $expense->expense_no ?? '',
            'party'       => $expense->party ?? '',
            'paymentType' => $expense->payment_type,
            'amount'      => $expense->total_amount,
            'balance'     => $expense->balance,
        ]]);
    }

    public function destroyExpense($id)
    {
        $expense = Expense::where('user_id', Auth::id())->findOrFail($id);
        $expense->delete();
        return response()->json(['success' => true]);
    }
}
