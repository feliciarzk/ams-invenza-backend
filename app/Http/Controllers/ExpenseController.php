<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::with([
            'location',
            'creator',
        ]);

        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('expense_number')) {
            $query->where(
                'expense_number',
                'ilike',
                '%' . $request->expense_number . '%'
            );
        }

        if ($request->filled('expense_date')) {
            $query->whereDate('expense_date', $request->expense_date);
        }

        $expenses = $query
            ->orderByDesc('expense_date')
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $expenses,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'location_id' => [
                'required',
                'integer',
                'exists:locations,id',
            ],
            'expense_number' => [
                'required',
                'string',
                'max:100',
                'unique:expenses,expense_number',
            ],
            'expense_date' => [
                'required',
                'date',
            ],
            'category' => [
                'required',
                'string',
                'max:100',
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'amount' => [
                'required',
                'numeric',
                'min:0',
            ],
            'status' => [
                'required',
                'string',
                'max:50',
            ],
        ]);

        $validated['created_by'] = auth()->id();

        $expense = Expense::create($validated);

        return response()->json([
            'success' => true,
            'data' => $expense->fresh()->load([
                'location',
                'creator',
            ]),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $expense = Expense::find($id);

        if (!$expense) {
            return response()->json([
                'success' => false,
                'message' => 'Expense not found.',
            ], 404);
        }

        $validated = $request->validate([
            'location_id' => [
                'required',
                'integer',
                'exists:locations,id',
            ],
            'expense_number' => [
                'required',
                'string',
                'max:100',
                'unique:expenses,expense_number,' . $expense->id,
            ],
            'expense_date' => [
                'required',
                'date',
            ],
            'category' => [
                'required',
                'string',
                'max:100',
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'amount' => [
                'required',
                'numeric',
                'min:0',
            ],
            'status' => [
                'required',
                'string',
                'max:50',
            ],
        ]);

        $expense->update($validated);

        return response()->json([
            'success' => true,
            'data' => $expense->fresh()->load([
                'location',
                'creator',
            ]),
        ]);
    }

    public function destroy($id)
    {
        $expense = Expense::find($id);

        if (!$expense) {
            return response()->json([
                'success' => false,
                'message' => 'Expense not found.',
            ], 404);
        }

        $expense->delete();

        return response()->json([
            'success' => true,
            'message' => 'Expense deleted successfully.',
        ]);
    }
}
