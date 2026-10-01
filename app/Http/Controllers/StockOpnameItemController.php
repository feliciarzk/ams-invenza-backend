<?php

namespace App\Http\Controllers;

use App\Models\StockOpnameItem;
use Illuminate\Http\Request;

class StockOpnameItemController extends Controller
{
    public function index(Request $request)
    {
        $query = StockOpnameItem::with([
            'opname',
            'item',
        ]);

        if ($request->filled('opname_id')) {
            $query->where('opname_id', $request->opname_id);
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
            'opname_id' => [
                'required',
                'integer',
                'exists:stock_opnames,id',
            ],
            'item_id' => [
                'required',
                'integer',
                'exists:items,id',
            ],
            'system_quantity' => [
                'required',
                'numeric',
                'min:0',
            ],
            'physical_quantity' => [
                'required',
                'numeric',
                'min:0',
            ],
        ]);

        $item = StockOpnameItem::create($validated);

        return response()->json([
            'success' => true,
            'data' => $item->fresh()->load([
                'opname',
                'item',
            ]),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $item = StockOpnameItem::find($id);

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Stock opname item not found.',
            ], 404);
        }

        $validated = $request->validate([
            'opname_id' => [
                'required',
                'integer',
                'exists:stock_opnames,id',
            ],
            'item_id' => [
                'required',
                'integer',
                'exists:items,id',
            ],
            'system_quantity' => [
                'required',
                'numeric',
                'min:0',
            ],
            'physical_quantity' => [
                'required',
                'numeric',
                'min:0',
            ],
        ]);

        $item->update($validated);

        return response()->json([
            'success' => true,
            'data' => $item->fresh()->load([
                'opname',
                'item',
            ]),
        ]);
    }

    public function destroy($id)
    {
        $item = StockOpnameItem::find($id);

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Stock opname item not found.',
            ], 404);
        }

        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Stock opname item deleted successfully.',
        ]);
    }
}