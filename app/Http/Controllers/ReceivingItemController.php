<?php

namespace App\Http\Controllers;

use App\Models\ReceivingItem;
use Illuminate\Http\Request;

class ReceivingItemController extends Controller
{
    public function index(Request $request)
    {
        $query = ReceivingItem::with([
            'receiving',
            'item',
        ]);

        if ($request->filled('receiving_id')) {
            $query->where('receiving_id', $request->receiving_id);
        }

        if ($request->filled('item_id')) {
            $query->where('item_id', $request->item_id);
        }

        $receivingItems = $query
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $receivingItems,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'receiving_id' => [
                'required',
                'integer',
                'exists:receivings,id',
            ],
            'item_id' => [
                'required',
                'integer',
                'exists:items,id',
            ],
            'quantity_received' => [
                'required',
                'numeric',
                'gt:0',
            ],
        ]);

        $receivingItem = ReceivingItem::create($validated);

        return response()->json([
            'success' => true,
            'data' => $receivingItem->fresh()->load([
                'receiving',
                'item',
            ]),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $receivingItem = ReceivingItem::find($id);

        if (!$receivingItem) {
            return response()->json([
                'success' => false,
                'message' => 'Receiving item not found.',
            ], 404);
        }

        $validated = $request->validate([
            'receiving_id' => [
                'required',
                'integer',
                'exists:receivings,id',
            ],
            'item_id' => [
                'required',
                'integer',
                'exists:items,id',
            ],
            'quantity_received' => [
                'required',
                'numeric',
                'gt:0',
            ],
        ]);

        $receivingItem->update($validated);

        return response()->json([
            'success' => true,
            'data' => $receivingItem->fresh()->load([
                'receiving',
                'item',
            ]),
        ]);
    }

    public function destroy($id)
    {
        $receivingItem = ReceivingItem::find($id);

        if (!$receivingItem) {
            return response()->json([
                'success' => false,
                'message' => 'Receiving item not found.',
            ], 404);
        }

        $receivingItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Receiving item deleted successfully.',
        ]);
    }
}