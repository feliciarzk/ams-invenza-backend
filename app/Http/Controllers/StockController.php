<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Stock::query()
                ->with(['location', 'item']);

            if ($request->filled('location_id')) {
                $query->where('location_id', $request->location_id);
            }

            if ($request->filled('item_id')) {
                $query->where('item_id', $request->item_id);
            }

            $stocks = $query
                ->orderBy('location_id')
                ->orderBy('item_id')
                ->get()
                ->map(function ($stock) {
                    return [
                        'id' => $stock->id,
                        'location_id' => $stock->location_id,
                        'location' => $stock->location?->name,
                        'item_id' => $stock->item_id,
                        'item' => $stock->item?->name,
                        'item_code' => $stock->item?->code,
                        'quantity' => $stock->quantity,
                        'updated_at' => $stock->updated_at,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $stocks,
            ]);
        } catch (\Throwable $error) {
            return response()->json([
                'success' => false,
                'message' => $error->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'location_id' => ['required', 'integer', 'exists:locations,id'],
                'item_id' => ['required', 'integer', 'exists:items,id'],
                'quantity' => ['required', 'numeric', 'min:0'],
            ]);

            $existingStock = Stock::where('location_id', $validated['location_id'])
                ->where('item_id', $validated['item_id'])
                ->first();

            if ($existingStock) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stock for this item at this location already exists',
                ], 409);
            }

            $stock = Stock::create([
                'location_id' => $validated['location_id'],
                'item_id' => $validated['item_id'],
                'quantity' => $validated['quantity'],
            ]);

            return response()->json([
                'success' => true,
                'data' => $stock->load(['location', 'item']),
            ], 201);
        } catch (\Throwable $error) {
            return response()->json([
                'success' => false,
                'message' => $error->getMessage(),
            ], 400);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $stock = Stock::find($id);

            if (!$stock) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stock not found',
                ], 404);
            }

            $validated = $request->validate([
                'location_id' => ['required', 'integer', 'exists:locations,id'],
                'item_id' => ['required', 'integer', 'exists:items,id'],
                'quantity' => ['required', 'numeric', 'min:0'],
            ]);

            $duplicate = Stock::where('location_id', $validated['location_id'])
                ->where('item_id', $validated['item_id'])
                ->where('id', '!=', $id)
                ->exists();

            if ($duplicate) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stock for this item at this location already exists',
                ], 409);
            }

            $stock->update([
                'location_id' => $validated['location_id'],
                'item_id' => $validated['item_id'],
                'quantity' => $validated['quantity'],
            ]);

            return response()->json([
                'success' => true,
                'data' => $stock->fresh()->load(['location', 'item']),
            ]);
        } catch (\Throwable $error) {
            return response()->json([
                'success' => false,
                'message' => $error->getMessage(),
            ], 400);
        }
    }

    public function destroy($id)
    {
        try {
            $stock = Stock::find($id);

            if (!$stock) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stock not found',
                ], 404);
            }

            $stock->delete();

            return response()->json([
                'success' => true,
                'message' => 'Stock deleted successfully',
            ]);
        } catch (\Throwable $error) {
            return response()->json([
                'success' => false,
                'message' => $error->getMessage(),
            ], 400);
        }
    }
}