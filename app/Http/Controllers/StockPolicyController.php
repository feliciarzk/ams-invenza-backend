<?php

namespace App\Http\Controllers;

use App\Models\StockPolicy;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StockPolicyController extends Controller
{
    public function index(Request $request)
    {
        $query = StockPolicy::with(['location', 'item']);

        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        if ($request->filled('item_id')) {
            $query->where('item_id', $request->item_id);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', filter_var(
                $request->is_active,
                FILTER_VALIDATE_BOOLEAN
            ));
        }

        $stockPolicies = $query
            ->orderBy('location_id')
            ->orderBy('item_id')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $stockPolicies,
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
            'item_id' => [
                'required',
                'integer',
                'exists:items,id',
            ],
            'minimum_stock' => [
                'required',
                'numeric',
                'min:0',
            ],
            'maximum_stock' => [
                'nullable',
                'numeric',
                'gte:minimum_stock',
            ],
            'reorder_quantity' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            'is_active' => [
                'sometimes',
                'boolean',
            ],
        ]);

        $exists = StockPolicy::where('location_id', $validated['location_id'])
            ->where('item_id', $validated['item_id'])
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Stock policy for this location and item already exists.',
            ], 422);
        }

        $stockPolicy = StockPolicy::create($validated);

        return response()->json([
            'success' => true,
            'data' => $stockPolicy->load(['location', 'item']),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $stockPolicy = StockPolicy::find($id);

        if (!$stockPolicy) {
            return response()->json([
                'success' => false,
                'message' => 'Stock policy not found.',
            ], 404);
        }

        $validated = $request->validate([
            'location_id' => [
                'required',
                'integer',
                'exists:locations,id',
            ],
            'item_id' => [
                'required',
                'integer',
                'exists:items,id',
            ],
            'minimum_stock' => [
                'required',
                'numeric',
                'min:0',
            ],
            'maximum_stock' => [
                'nullable',
                'numeric',
                'gte:minimum_stock',
            ],
            'reorder_quantity' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            'is_active' => [
                'sometimes',
                'boolean',
            ],
        ]);

        $exists = StockPolicy::where('location_id', $validated['location_id'])
            ->where('item_id', $validated['item_id'])
            ->where('id', '!=', $stockPolicy->id)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Stock policy for this location and item already exists.',
            ], 422);
        }

        $stockPolicy->update($validated);

        return response()->json([
            'success' => true,
            'data' => $stockPolicy->fresh()->load(['location', 'item']),
        ]);
    }

    public function destroy($id)
    {
        $stockPolicy = StockPolicy::find($id);

        if (!$stockPolicy) {
            return response()->json([
                'success' => false,
                'message' => 'Stock policy not found.',
            ], 404);
        }

        $stockPolicy->delete();

        return response()->json([
            'success' => true,
            'message' => 'Stock policy deleted successfully',
        ]);
    }
}