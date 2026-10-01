<?php

namespace App\Http\Controllers;

use App\Models\Transfer;
use Illuminate\Http\Request;

class TransferController extends Controller
{
    public function index(Request $request)
    {
        $query = Transfer::with([
            'fromLocation',
            'toLocation',
            'requester',
            'approver',
        ]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('from_location_id')) {
            $query->where('from_location_id', $request->from_location_id);
        }

        if ($request->filled('to_location_id')) {
            $query->where('to_location_id', $request->to_location_id);
        }

        if ($request->filled('transfer_number')) {
            $query->where(
                'transfer_number',
                'ilike',
                '%' . $request->transfer_number . '%'
            );
        }

        $transfers = $query
            ->orderByDesc('requested_at')
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $transfers,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'transfer_number' => [
                'required',
                'string',
                'max:100',
                'unique:transfers,transfer_number',
            ],
            'from_location_id' => [
                'required',
                'integer',
                'exists:locations,id',
            ],
            'to_location_id' => [
                'required',
                'integer',
                'exists:locations,id',
                'different:from_location_id',
            ],
            'status' => [
                'sometimes',
                'string',
                'max:50',
            ],
            'requested_at' => [
                'sometimes',
                'date',
            ],
            'approved_at' => [
                'nullable',
                'date',
            ],
            'received_at' => [
                'nullable',
                'date',
            ],
            'notes' => [
                'nullable',
                'string',
            ],
        ]);

        $validated['requested_by'] = auth()->id();

        $transfer = Transfer::create($validated);

        return response()->json([
            'success' => true,
            'data' => $transfer->load([
                'fromLocation',
                'toLocation',
                'requester',
                'approver',
            ]),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $transfer = Transfer::find($id);

        if (!$transfer) {
            return response()->json([
                'success' => false,
                'message' => 'Transfer not found.',
            ], 404);
        }

        $validated = $request->validate([
            'transfer_number' => [
                'required',
                'string',
                'max:100',
                'unique:transfers,transfer_number,' . $transfer->id,
            ],
            'from_location_id' => [
                'required',
                'integer',
                'exists:locations,id',
            ],
            'to_location_id' => [
                'required',
                'integer',
                'exists:locations,id',
                'different:from_location_id',
            ],
            'status' => [
                'sometimes',
                'string',
                'max:50',
            ],
            'requested_at' => [
                'sometimes',
                'date',
            ],
            'approved_at' => [
                'nullable',
                'date',
            ],
            'received_at' => [
                'nullable',
                'date',
            ],
            'notes' => [
                'nullable',
                'string',
            ],
        ]);

        $transfer->update($validated);

        return response()->json([
            'success' => true,
            'data' => $transfer->fresh()->load([
                'fromLocation',
                'toLocation',
                'requester',
                'approver',
            ]),
        ]);
    }

    public function destroy($id)
    {
        $transfer = Transfer::find($id);

        if (!$transfer) {
            return response()->json([
                'success' => false,
                'message' => 'Transfer not found.',
            ], 404);
        }

        $transfer->delete();

        return response()->json([
            'success' => true,
            'message' => 'Transfer deleted successfully.',
        ]);
    }
}