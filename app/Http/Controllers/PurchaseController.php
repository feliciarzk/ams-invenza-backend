<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $query = Purchase::with([
            'supplier',
            'location',
            'creator',
            'purchaseItems.item',
        ]);

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('purchase_number')) {
            $query->where(
                'purchase_number',
                'ilike',
                '%' . $request->purchase_number . '%'
            );
        }

        $purchases = $query
            ->orderByDesc('purchase_date')
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $purchases,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => [
                'nullable',
                'integer',
                'exists:suppliers,id',
            ],
            'location_id' => [
                'required',
                'integer',
                'exists:locations,id',
            ],
            'purchase_number' => [
                'required',
                'string',
                'max:100',
                'unique:purchases,purchase_number',
            ],
            'status' => [
                'sometimes',
                'string',
                'max:50',
            ],
            'purchase_date' => [
                'required',
                'date',
            ],
            'total_amount' => [
                'sometimes',
                'numeric',
                'min:0',
            ],
            'notes' => [
                'nullable',
                'string',
            ],
        ]);

        $validated['created_by'] = auth()->id();

        $purchase = Purchase::create($validated);

        return response()->json([
            'success' => true,
            'data' => $purchase->load([
                'supplier',
                'location',
                'creator',
                'purchaseItems.item',
            ]),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $purchase = Purchase::find($id);

        if (!$purchase) {
            return response()->json([
                'success' => false,
                'message' => 'Purchase not found.',
            ], 404);
        }

        $validated = $request->validate([
            'supplier_id' => [
                'nullable',
                'integer',
                'exists:suppliers,id',
            ],
            'location_id' => [
                'required',
                'integer',
                'exists:locations,id',
            ],
            'purchase_number' => [
                'required',
                'string',
                'max:100',
                'unique:purchases,purchase_number,' . $purchase->id,
            ],
            'status' => [
                'sometimes',
                'string',
                'max:50',
            ],
            'purchase_date' => [
                'required',
                'date',
            ],
            'total_amount' => [
                'sometimes',
                'numeric',
                'min:0',
            ],
            'notes' => [
                'nullable',
                'string',
            ],
        ]);

        $purchase->update($validated);

        return response()->json([
            'success' => true,
            'data' => $purchase->fresh()->load([
                'supplier',
                'location',
                'creator',
                'purchaseItems.item',
            ]),
        ]);
    }

    public function destroy($id)
    {
        $purchase = Purchase::find($id);

        if (!$purchase) {
            return response()->json([
                'success' => false,
                'message' => 'Purchase not found.',
            ], 404);
        }

        DB::transaction(function () use ($purchase) {
            $purchase->purchaseItems()->delete();
            $purchase->delete();
        });

        return response()->json([
            'success' => true,
            'message' => 'Purchase deleted successfully.',
        ]);
    }
}