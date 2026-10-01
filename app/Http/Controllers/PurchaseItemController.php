<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use Illuminate\Http\Request;

class PurchaseItemController extends Controller
{
    public function index(Request $request)
    {
        $query = PurchaseItem::with([
            'purchase',
            'item',
        ]);

        if ($request->filled('purchase_id')) {
            $query->where('purchase_id', $request->purchase_id);
        }

        if ($request->filled('item_id')) {
            $query->where('item_id', $request->item_id);
        }

        $purchaseItems = $query
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $purchaseItems,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'purchase_id' => [
                'required',
                'integer',
                'exists:purchases,id',
            ],
            'item_id' => [
                'required',
                'integer',
                'exists:items,id',
            ],
            'quantity' => [
                'required',
                'numeric',
                'gt:0',
            ],
            'unit_price' => [
                'required',
                'numeric',
                'min:0',
            ],
        ]);

        $purchaseItem = PurchaseItem::create($validated);

        $this->updatePurchaseTotal($validated['purchase_id']);

        return response()->json([
            'success' => true,
            'data' => $purchaseItem->fresh()->load([
                'purchase',
                'item',
            ]),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $purchaseItem = PurchaseItem::find($id);

        if (!$purchaseItem) {
            return response()->json([
                'success' => false,
                'message' => 'Purchase item not found.',
            ], 404);
        }

        $validated = $request->validate([
            'purchase_id' => [
                'required',
                'integer',
                'exists:purchases,id',
            ],
            'item_id' => [
                'required',
                'integer',
                'exists:items,id',
            ],
            'quantity' => [
                'required',
                'numeric',
                'gt:0',
            ],
            'unit_price' => [
                'required',
                'numeric',
                'min:0',
            ],
        ]);

        $oldPurchaseId = $purchaseItem->purchase_id;

        $purchaseItem->update($validated);

        $this->updatePurchaseTotal($oldPurchaseId);

        if ($oldPurchaseId != $validated['purchase_id']) {
            $this->updatePurchaseTotal($validated['purchase_id']);
        }

        return response()->json([
            'success' => true,
            'data' => $purchaseItem->fresh()->load([
                'purchase',
                'item',
            ]),
        ]);
    }

    public function destroy($id)
    {
        $purchaseItem = PurchaseItem::find($id);

        if (!$purchaseItem) {
            return response()->json([
                'success' => false,
                'message' => 'Purchase item not found.',
            ], 404);
        }

        $purchaseId = $purchaseItem->purchase_id;

        $purchaseItem->delete();

        $this->updatePurchaseTotal($purchaseId);

        return response()->json([
            'success' => true,
            'message' => 'Purchase item deleted successfully.',
        ]);
    }

    private function updatePurchaseTotal($purchaseId)
    {
        $total = PurchaseItem::where('purchase_id', $purchaseId)
            ->sum('subtotal');

        $purchase = Purchase::find($purchaseId);

        if ($purchase) {
            $purchase->update([
                'total_amount' => $total,
            ]);
        }
    }
}