<?php

namespace App\Http\Controllers;

use App\Models\TransferItem;
use Illuminate\Http\Request;

class TransferItemController extends Controller
{
    public function index(Request $request)
    {
        $query = TransferItem::with([
            'transfer',
            'item',
        ]);

        if ($request->filled('transfer_id')) {
            $query->where('transfer_id', $request->transfer_id);
        }

        if ($request->filled('item_id')) {
            $query->where('item_id', $request->item_id);
        }

        $items = $query
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $items,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'transfer_id' => [
                'required',
                'integer',
                'exists:transfers,id',
            ],
            'item_id' => [
                'required',
                'integer',
                'exists:items,id',
            ],
            'quantity_requested' => [
                'required',
                'numeric',
                'gt:0',
            ],
            'quantity_sent' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            'quantity_received' => [
                'nullable',
                'numeric',
                'min:0',
            ],
        ]);

        $item = TransferItem::create($validated);

        return response()->json([
            'success' => true,
            'data' => $item->fresh()->load([
                'transfer',
                'item',
            ]),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $item = TransferItem::find($id);

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Transfer item not found.',
            ], 404);
        }

        $validated = $request->validate([
            'transfer_id' => [
                'required',
                'integer',
                'exists:transfers,id',
            ],
            'item_id' => [
                'required',
                'integer',
                'exists:items,id',
            ],
            'quantity_requested' => [
                'required',
                'numeric',
                'gt:0',
            ],
            'quantity_sent' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            'quantity_received' => [
                'nullable',
                'numeric',
                'min:0',
            ],
        ]);

        $item->update($validated);

        return response()->json([
            'success' => true,
            'data' => $item->fresh()->load([
                'transfer',
                'item',
            ]),
        ]);
    }

    public function destroy($id)
    {
        $item = TransferItem::find($id);

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Transfer item not found.',
            ], 404);
        }

        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Transfer item deleted successfully.',
        ]);
    }
}