<?php

namespace App\Http\Controllers;

use App\Models\StockMovement;
use Illuminate\Http\Request;

class StockMovementController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = StockMovement::query()
                ->with([
                    'item',
                    'fromLocation',
                    'toLocation',
                    'creator',
                ]);

            if ($request->filled('item_id')) {
                $query->where('item_id', $request->item_id);
            }

            if ($request->filled('movement_type')) {
                $query->where('movement_type', $request->movement_type);
            }

            if ($request->filled('from_location_id')) {
                $query->where('from_location_id', $request->from_location_id);
            }

            if ($request->filled('to_location_id')) {
                $query->where('to_location_id', $request->to_location_id);
            }

            $movements = $query
                ->orderByDesc('created_at')
                ->get()
                ->map(function ($movement) {
                    return [
                        'id' => $movement->id,
                        'item_id' => $movement->item_id,
                        'item' => $movement->item?->name,
                        'item_code' => $movement->item?->code,
                        'from_location_id' => $movement->from_location_id,
                        'from_location' => $movement->fromLocation?->name,
                        'to_location_id' => $movement->to_location_id,
                        'to_location' => $movement->toLocation?->name,
                        'movement_type' => $movement->movement_type,
                        'quantity' => $movement->quantity,
                        'reference_type' => $movement->reference_type,
                        'reference_id' => $movement->reference_id,
                        'notes' => $movement->notes,
                        'created_by' => $movement->created_by,
                        'created_by_name' => $movement->creator?->name,
                        'created_at' => $movement->created_at,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $movements,
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
                'item_id' => ['required', 'integer', 'exists:items,id'],
                'from_location_id' => ['nullable', 'integer', 'exists:locations,id'],
                'to_location_id' => ['nullable', 'integer', 'exists:locations,id'],
                'movement_type' => ['required', 'string'],
                'quantity' => ['required', 'numeric', 'min:0.01'],
                'reference_type' => ['nullable', 'string'],
                'reference_id' => ['nullable', 'integer'],
                'notes' => ['nullable', 'string'],
            ]);

            if (
                empty($validated['from_location_id']) &&
                empty($validated['to_location_id'])
            ) {
                return response()->json([
                    'success' => false,
                    'message' => 'At least one source or destination location is required',
                ], 422);
            }

            $movement = StockMovement::create([
                'item_id' => $validated['item_id'],
                'from_location_id' => $validated['from_location_id'] ?? null,
                'to_location_id' => $validated['to_location_id'] ?? null,
                'movement_type' => $validated['movement_type'],
                'quantity' => $validated['quantity'],
                'reference_type' => $validated['reference_type'] ?? null,
                'reference_id' => $validated['reference_id'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'data' => $movement->load([
                    'item',
                    'fromLocation',
                    'toLocation',
                    'creator',
                ]),
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
            $movement = StockMovement::find($id);

            if (!$movement) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stock movement not found',
                ], 404);
            }

            $validated = $request->validate([
                'item_id' => ['required', 'integer', 'exists:items,id'],
                'from_location_id' => ['nullable', 'integer', 'exists:locations,id'],
                'to_location_id' => ['nullable', 'integer', 'exists:locations,id'],
                'movement_type' => ['required', 'string'],
                'quantity' => ['required', 'numeric', 'min:0.01'],
                'reference_type' => ['nullable', 'string'],
                'reference_id' => ['nullable', 'integer'],
                'notes' => ['nullable', 'string'],
            ]);

            if (
                empty($validated['from_location_id']) &&
                empty($validated['to_location_id'])
            ) {
                return response()->json([
                    'success' => false,
                    'message' => 'At least one source or destination location is required',
                ], 422);
            }

            $movement->update([
                'item_id' => $validated['item_id'],
                'from_location_id' => $validated['from_location_id'] ?? null,
                'to_location_id' => $validated['to_location_id'] ?? null,
                'movement_type' => $validated['movement_type'],
                'quantity' => $validated['quantity'],
                'reference_type' => $validated['reference_type'] ?? null,
                'reference_id' => $validated['reference_id'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            return response()->json([
                'success' => true,
                'data' => $movement->fresh()->load([
                    'item',
                    'fromLocation',
                    'toLocation',
                    'creator',
                ]),
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
            $movement = StockMovement::find($id);

            if (!$movement) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stock movement not found',
                ], 404);
            }

            $movement->delete();

            return response()->json([
                'success' => true,
                'message' => 'Stock movement deleted successfully',
            ]);
        } catch (\Throwable $error) {
            return response()->json([
                'success' => false,
                'message' => $error->getMessage(),
            ], 400);
        }
    }
}