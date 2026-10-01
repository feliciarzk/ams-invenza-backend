<?php

namespace App\Http\Controllers;

use App\Models\MenuPrice;
use Illuminate\Http\Request;

class MenuPriceController extends Controller
{
    public function index(Request $request)
    {
        $query = MenuPrice::with([
            'item',
            'location',
        ]);

        if ($request->filled('item_id')) {
            $query->where('item_id', $request->item_id);
        }

        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        if ($request->filled('is_available')) {
            $query->where(
                'is_available',
                filter_var($request->is_available, FILTER_VALIDATE_BOOLEAN)
            );
        }

        $menuPrices = $query
            ->orderBy('location_id')
            ->orderBy('item_id')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $menuPrices,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_id' => [
                'required',
                'integer',
                'exists:items,id',
            ],
            'location_id' => [
                'required',
                'integer',
                'exists:locations,id',
            ],
            'selling_price' => [
                'required',
                'numeric',
                'min:0',
            ],
            'hpp' => [
                'required',
                'numeric',
                'min:0',
            ],
            'is_available' => [
                'sometimes',
                'boolean',
            ],
        ]);

        $menuPrice = MenuPrice::create($validated);

        return response()->json([
            'success' => true,
            'data' => $menuPrice->fresh()->load([
                'item',
                'location',
            ]),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $menuPrice = MenuPrice::find($id);

        if (!$menuPrice) {
            return response()->json([
                'success' => false,
                'message' => 'Menu price not found.',
            ], 404);
        }

        $validated = $request->validate([
            'item_id' => [
                'required',
                'integer',
                'exists:items,id',
            ],
            'location_id' => [
                'required',
                'integer',
                'exists:locations,id',
            ],
            'selling_price' => [
                'required',
                'numeric',
                'min:0',
            ],
            'hpp' => [
                'required',
                'numeric',
                'min:0',
            ],
            'is_available' => [
                'sometimes',
                'boolean',
            ],
        ]);

        $menuPrice->update($validated);

        return response()->json([
            'success' => true,
            'data' => $menuPrice->fresh()->load([
                'item',
                'location',
            ]),
        ]);
    }

    public function destroy($id)
    {
        $menuPrice = MenuPrice::find($id);

        if (!$menuPrice) {
            return response()->json([
                'success' => false,
                'message' => 'Menu price not found.',
            ], 404);
        }

        $menuPrice->delete();

        return response()->json([
            'success' => true,
            'message' => 'Menu price deleted successfully.',
        ]);
    }
}