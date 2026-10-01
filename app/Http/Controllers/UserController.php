<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['role', 'location']);

        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', filter_var(
                $request->is_active,
                FILTER_VALIDATE_BOOLEAN
            ));
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', '%' . $search . '%')
                    ->orWhere('email', 'ilike', '%' . $search . '%')
                    ->orWhere('position', 'ilike', '%' . $search . '%');
            });
        }

        $users = $query
            ->orderBy('id')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'position' => $user->position,
                    'email' => $user->email,
                    'role_id' => $user->role_id,
                    'role' => $user->role?->name,
                    'location_id' => $user->location_id,
                    'location' => $user->location?->name,
                    'is_active' => $user->is_active,
                ];
            }),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'role_id' => [
                'required',
                'integer',
                'exists:roles,id',
            ],
            'location_id' => [
                'nullable',
                'integer',
                'exists:locations,id',
            ],
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'position' => [
                'nullable',
                'string',
                'max:255',
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],
            'password' => [
                'required',
                'string',
                'min:6',
            ],
            'is_active' => [
                'sometimes',
                'boolean',
            ],
        ]);

        $user = User::create($validated);

        return response()->json([
            'success' => true,
            'data' => $user->fresh()->load([
                'role',
                'location',
            ]),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        $validated = $request->validate([
            'role_id' => [
                'required',
                'integer',
                'exists:roles,id',
            ],
            'location_id' => [
                'nullable',
                'integer',
                'exists:locations,id',
            ],
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'position' => [
                'nullable',
                'string',
                'max:255',
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email,' . $user->id,
            ],
            'password' => [
                'nullable',
                'string',
                'min:6',
            ],
            'is_active' => [
                'sometimes',
                'boolean',
            ],
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $user->update($validated);

        return response()->json([
            'success' => true,
            'data' => $user->fresh()->load([
                'role',
                'location',
            ]),
        ]);
    }

    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully.',
        ]);
    }
}