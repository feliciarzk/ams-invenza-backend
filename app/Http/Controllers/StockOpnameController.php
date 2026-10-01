<?php

namespace App\Http\Controllers;

use App\Models\StockOpname;
use Illuminate\Http\Request;

class StockOpnameController extends Controller
{
    public function index(Request $request)
    {
        $query = StockOpname::with([
            'location',
            'creator',
            'completer',
        ]);

        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('opname_number')) {
            $query->where(
                'opname_number',
                'ilike',
                '%' . $request->opname_number . '%'
            );
        }

        $opnames = $query
            ->orderByDesc('opname_date')
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $opnames,
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
            'opname_number' => [
                'required',
                'string',
                'max:100',
                'unique:stock_opnames,opname_number',
            ],
            'status' => [
                'sometimes',
                'string',
                'max:50',
            ],
            'opname_date' => [
                'required',
                'date',
            ],
            'notes' => [
                'nullable',
                'string',
            ],
        ]);

        $validated['created_by'] = auth()->id();

        $opname = StockOpname::create($validated);

        return response()->json([
            'success' => true,
            'data' => $opname->load([
                'location',
                'creator',
                'completer',
            ]),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $opname = StockOpname::find($id);

        if (!$opname) {
            return response()->json([
                'success' => false,
                'message' => 'Stock opname not found.',
            ], 404);
        }

        $validated = $request->validate([
            'location_id' => [
                'required',
                'integer',
                'exists:locations,id',
            ],
            'opname_number' => [
                'required',
                'string',
                'max:100',
                'unique:stock_opnames,opname_number,' . $opname->id,
            ],
            'status' => [
                'sometimes',
                'string',
                'max:50',
            ],
            'opname_date' => [
                'required',
                'date',
            ],
            'notes' => [
                'nullable',
                'string',
            ],
            'completed_by' => [
                'nullable',
                'integer',
                'exists:users,id',
            ],
            'completed_at' => [
                'nullable',
                'date',
            ],
        ]);

        $opname->update($validated);

        return response()->json([
            'success' => true,
            'data' => $opname->fresh()->load([
                'location',
                'creator',
                'completer',
            ]),
        ]);
    }

    public function destroy($id)
    {
        $opname = StockOpname::find($id);

        if (!$opname) {
            return response()->json([
                'success' => false,
                'message' => 'Stock opname not found.',
            ], 404);
        }

        $opname->delete();

        return response()->json([
            'success' => true,
            'message' => 'Stock opname deleted successfully.',
        ]);
    }
}