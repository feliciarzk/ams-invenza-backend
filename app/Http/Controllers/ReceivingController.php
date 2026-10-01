<?php

namespace App\Http\Controllers;

use App\Models\Receiving;
use Illuminate\Http\Request;

class ReceivingController extends Controller
{
    public function index(Request $request)
    {
        $query = Receiving::with([
            'purchase',
            'location',
            'receiver',
        ]);

        if ($request->filled('purchase_id')) {
            $query->where('purchase_id', $request->purchase_id);
        }

        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        if ($request->filled('receiving_number')) {
            $query->where(
                'receiving_number',
                'ilike',
                '%' . $request->receiving_number . '%'
            );
        }

        $receivings = $query
            ->orderByDesc('receiving_date')
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $receivings,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'purchase_id' => [
                'nullable',
                'integer',
                'exists:purchases,id',
            ],
            'location_id' => [
                'required',
                'integer',
                'exists:locations,id',
            ],
            'receiving_number' => [
                'required',
                'string',
                'max:100',
                'unique:receivings,receiving_number',
            ],
            'receiving_date' => [
                'required',
                'date',
            ],
            'notes' => [
                'nullable',
                'string',
            ],
        ]);

        $validated['received_by'] = auth()->id();

        $receiving = Receiving::create($validated);

        return response()->json([
            'success' => true,
            'data' => $receiving->load([
                'purchase',
                'location',
                'receiver',
            ]),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $receiving = Receiving::find($id);

        if (!$receiving) {
            return response()->json([
                'success' => false,
                'message' => 'Receiving not found.',
            ], 404);
        }

        $validated = $request->validate([
            'purchase_id' => [
                'nullable',
                'integer',
                'exists:purchases,id',
            ],
            'location_id' => [
                'required',
                'integer',
                'exists:locations,id',
            ],
            'receiving_number' => [
                'required',
                'string',
                'max:100',
                'unique:receivings,receiving_number,' . $receiving->id,
            ],
            'receiving_date' => [
                'required',
                'date',
            ],
            'notes' => [
                'nullable',
                'string',
            ],
        ]);

        $receiving->update($validated);

        return response()->json([
            'success' => true,
            'data' => $receiving->fresh()->load([
                'purchase',
                'location',
                'receiver',
            ]),
        ]);
    }

    public function destroy($id)
    {
        $receiving = Receiving::find($id);

        if (!$receiving) {
            return response()->json([
                'success' => false,
                'message' => 'Receiving not found.',
            ], 404);
        }

        $receiving->delete();

        return response()->json([
            'success' => true,
            'message' => 'Receiving deleted successfully.',
        ]);
    }
}