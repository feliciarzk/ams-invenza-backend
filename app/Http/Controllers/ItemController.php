<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Item::query()
                ->with('categories')
                ->where('is_active', true);

            if ($request->filled('category_id')) {
                $query->whereHas('categories', function ($categoryQuery) use ($request) {
                    $categoryQuery->where('categories.id', $request->category_id);
                });
            }

            if ($request->filled('item_type')) {
                $query->where('item_type', $request->item_type);
            }

            $items = $query
                ->orderBy('name')
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'code' => $item->code,
                        'name' => $item->name,
                        'item_type' => $item->item_type,
                        'unit' => $item->unit,
                        'description' => $item->description,
                        'cost_price' => $item->cost_price,
                        'is_active' => $item->is_active,
                        'created_at' => $item->created_at,
                        'categories' => $item->categories
                            ->pluck('name')
                            ->values(),
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $items,
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
                'code' => ['required', 'string'],
                'name' => ['required', 'string'],
                'item_type' => ['required', 'in:PRODUCT,MATERIAL,SUPPLY'],
                'unit' => ['nullable', 'string'],
                'description' => ['nullable', 'string'],
                'cost_price' => ['nullable', 'numeric'],
                'category_ids' => ['nullable', 'array'],
                'category_ids.*' => ['integer', 'exists:categories,id'],
            ]);

            $item = DB::transaction(function () use ($validated) {
                $item = Item::create([
                    'code' => $validated['code'],
                    'name' => $validated['name'],
                    'item_type' => $validated['item_type'],
                    'unit' => $validated['unit'] ?? 'pcs',
                    'description' => $validated['description'] ?? null,
                    'cost_price' => $validated['cost_price'] ?? null,
                    'is_active' => true,
                ]);

                if (isset($validated['category_ids'])) {
                    $item->categories()->sync($validated['category_ids']);
                }

                return $item;
            });

            return response()->json([
                'success' => true,
                'data' => $item->load('categories'),
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
            $item = Item::find($id);

            if (!$item) {
                return response()->json([
                    'success' => false,
                    'message' => 'Item not found',
                ], 404);
            }

            $validated = $request->validate([
                'code' => ['required', 'string'],
                'name' => ['required', 'string'],
                'item_type' => ['required', 'in:PRODUCT,MATERIAL,SUPPLY'],
                'unit' => ['nullable', 'string'],
                'description' => ['nullable', 'string'],
                'cost_price' => ['nullable', 'numeric'],
                'category_ids' => ['nullable', 'array'],
                'category_ids.*' => ['integer', 'exists:categories,id'],
                'is_active' => ['nullable', 'boolean'],
            ]);

            DB::transaction(function () use ($item, $validated) {
                $item->update([
                    'code' => $validated['code'],
                    'name' => $validated['name'],
                    'item_type' => $validated['item_type'],
                    'unit' => $validated['unit'] ?? 'pcs',
                    'description' => $validated['description'] ?? null,
                    'cost_price' => $validated['cost_price'] ?? null,
                    'is_active' => $validated['is_active'] ?? $item->is_active,
                ]);

                if (array_key_exists('category_ids', $validated)) {
                    $item->categories()->sync($validated['category_ids'] ?? []);
                }
            });

            return response()->json([
                'success' => true,
                'data' => $item->fresh()->load('categories'),
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
            $item = Item::find($id);

            if (!$item) {
                return response()->json([
                    'success' => false,
                    'message' => 'Item not found',
                ], 404);
            }

            DB::transaction(function () use ($item) {
                $item->categories()->detach();
                $item->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'Item deleted successfully',
            ]);
        } catch (\Throwable $error) {
            return response()->json([
                'success' => false,
                'message' => $error->getMessage(),
            ], 400);
        }
    }
}