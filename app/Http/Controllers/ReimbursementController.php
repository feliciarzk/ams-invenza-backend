<?php

namespace App\Http\Controllers;

use App\Models\Reimbursement;
use Illuminate\Http\Request;

class ReimbursementController extends Controller
{
    public function index(Request $request)
    {
        $query = Reimbursement::with([
            'location',
            'requester',
            'approver',
        ]);

        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        if ($request->filled('requester_id')) {
            $query->where('requester_id', $request->requester_id);
        }

        if ($request->filled('approved_by')) {
            $query->where('approved_by', $request->approved_by);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('reimbursement_number')) {
            $query->where(
                'reimbursement_number',
                'ilike',
                '%' . $request->reimbursement_number . '%'
            );
        }

        if ($request->filled('reimbursement_date')) {
            $query->whereDate(
                'reimbursement_date',
                $request->reimbursement_date
            );
        }

        $reimbursements = $query
            ->orderByDesc('reimbursement_date')
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $reimbursements,
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
            'requester_id' => [
                'required',
                'integer',
                'exists:users,id',
            ],
            'reimbursement_number' => [
                'required',
                'string',
                'max:100',
                'unique:reimbursements,reimbursement_number',
            ],
            'reimbursement_date' => [
                'required',
                'date',
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'amount' => [
                'required',
                'numeric',
                'min:0',
            ],
            'status' => [
                'required',
                'string',
                'max:50',
            ],
            'approved_by' => [
                'nullable',
                'integer',
                'exists:users,id',
            ],
            'paid_at' => [
                'nullable',
                'date',
            ],
        ]);

        $reimbursement = Reimbursement::create($validated);

        return response()->json([
            'success' => true,
            'data' => $reimbursement->fresh()->load([
                'location',
                'requester',
                'approver',
            ]),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $reimbursement = Reimbursement::find($id);

        if (!$reimbursement) {
            return response()->json([
                'success' => false,
                'message' => 'Reimbursement not found.',
            ], 404);
        }

        $validated = $request->validate([
            'location_id' => [
                'required',
                'integer',
                'exists:locations,id',
            ],
            'requester_id' => [
                'required',
                'integer',
                'exists:users,id',
            ],
            'reimbursement_number' => [
                'required',
                'string',
                'max:100',
                'unique:reimbursements,reimbursement_number,' . $reimbursement->id,
            ],
            'reimbursement_date' => [
                'required',
                'date',
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'amount' => [
                'required',
                'numeric',
                'min:0',
            ],
            'status' => [
                'required',
                'string',
                'max:50',
            ],
            'approved_by' => [
                'nullable',
                'integer',
                'exists:users,id',
            ],
            'paid_at' => [
                'nullable',
                'date',
            ],
        ]);

        $reimbursement->update($validated);

        return response()->json([
            'success' => true,
            'data' => $reimbursement->fresh()->load([
                'location',
                'requester',
                'approver',
            ]),
        ]);
    }

    public function destroy($id)
    {
        $reimbursement = Reimbursement::find($id);

        if (!$reimbursement) {
            return response()->json([
                'success' => false,
                'message' => 'Reimbursement not found.',
            ], 404);
        }

        $reimbursement->delete();

        return response()->json([
            'success' => true,
            'message' => 'Reimbursement deleted successfully.',
        ]);
    }
}