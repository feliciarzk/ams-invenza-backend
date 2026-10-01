<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Category::query()
                ->with('location')
                ->where('is_active', true);

            if ($request->filled('location_id')) {
                $query->where('location_id', $request->location_id);
            }

            if ($request->filled('category_type')) {
                $query->where('category_type', $request->category_type);
            }

            $categories = $query
                ->orderBy('name')
                ->get()
                ->map(function ($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                        'category_type' => $category->category_type,
                        'location_id' => $category->location_id,
                        'location' => $category->location?->name,
                        'description' => $category->description,
                        'is_active' => $category->is_active,
                        'created_at' => $category->created_at,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $categories,
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
                'name' => ['required', 'string'],
                'category_type' => ['required', 'in:MENU,INVENTORY'],
                'location_id' => ['nullable', 'integer'],
                'description' => ['nullable', 'string'],
            ]);

            $category = Category::create([
                'name' => $validated['name'],
                'category_type' => $validated['category_type'],
                'location_id' => $validated['location_id'] ?? null,
                'description' => $validated['description'] ?? null,
                'is_active' => true,
            ]);

            return response()->json([
                'success' => true,
                'data' => $category,
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
            $category = Category::find($id);

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category not found',
                ], 404);
            }

            $validated = $request->validate([
                'name' => ['required', 'string'],
                'category_type' => ['required', 'in:MENU,INVENTORY'],
                'location_id' => ['nullable', 'integer'],
                'description' => ['nullable', 'string'],
                'is_active' => ['nullable', 'boolean'],
            ]);

            $category->update([
                'name' => $validated['name'],
                'category_type' => $validated['category_type'],
                'location_id' => $validated['location_id'] ?? null,
                'description' => $validated['description'] ?? null,
                'is_active' => $validated['is_active'] ?? $category->is_active,
            ]);

            return response()->json([
                'success' => true,
                'data' => $category->fresh(),
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
            $category = Category::find($id);

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category not found',
                ], 404);
            }

            $category->delete();

            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully',
            ]);
        } catch (\Throwable $error) {
            return response()->json([
                'success' => false,
                'message' => $error->getMessage(),
            ], 400);
        }
    }
}