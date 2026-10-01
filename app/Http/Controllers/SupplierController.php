<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        try {
            $suppliers = Supplier::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get([
                    'id',
                    'name',
                    'contact_person',
                    'phone',
                    'email',
                    'address',
                    'notes',
                    'is_active',
                    'created_at',
                ]);

            return response()->json([
                'success' => true,
                'data' => $suppliers,
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
                'contact_person' => ['nullable', 'string'],
                'phone' => ['nullable', 'string'],
                'email' => ['nullable', 'email'],
                'address' => ['nullable', 'string'],
                'notes' => ['nullable', 'string'],
            ]);

            $supplier = Supplier::create([
                'name' => $validated['name'],
                'contact_person' => $validated['contact_person'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'email' => $validated['email'] ?? null,
                'address' => $validated['address'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'is_active' => true,
            ]);

            return response()->json([
                'success' => true,
                'data' => $supplier,
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
            $supplier = Supplier::find($id);

            if (!$supplier) {
                return response()->json([
                    'success' => false,
                    'message' => 'Supplier not found',
                ], 404);
            }

            $validated = $request->validate([
                'name' => ['required', 'string'],
                'contact_person' => ['nullable', 'string'],
                'phone' => ['nullable', 'string'],
                'email' => ['nullable', 'email'],
                'address' => ['nullable', 'string'],
                'notes' => ['nullable', 'string'],
                'is_active' => ['nullable', 'boolean'],
            ]);

            $supplier->update([
                'name' => $validated['name'],
                'contact_person' => $validated['contact_person'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'email' => $validated['email'] ?? null,
                'address' => $validated['address'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'is_active' => $validated['is_active'] ?? $supplier->is_active,
            ]);

            return response()->json([
                'success' => true,
                'data' => $supplier->fresh(),
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
            $supplier = Supplier::find($id);

            if (!$supplier) {
                return response()->json([
                    'success' => false,
                    'message' => 'Supplier not found',
                ], 404);
            }

            $supplier->delete();

            return response()->json([
                'success' => true,
                'message' => 'Supplier deleted successfully',
            ]);
        } catch (\Throwable $error) {
            return response()->json([
                'success' => false,
                'message' => $error->getMessage(),
            ], 400);
        }
    }
}