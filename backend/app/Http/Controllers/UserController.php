<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateDetailsUserRequest;
use App\Http\Requests\User\UpdatePasswordUserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\User\UserListResource;
use App\Http\Resources\User\UserResource;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::query()->paginate();
        $transformedUsers = UserListResource::collection($users);

        return response()->json([
            'message' => 'Users retrieved successfully',
            'data' => $transformedUsers,
            'pagination' => [
                'total' => $users->total(),
                'per_page' => $users->perPage(),
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'next_page_url' => $users->nextPageUrl(),
                'prev_page_url' => $users->previousPageUrl(),
            ]
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();

        $user = User::create($validated + [
            'password' => Hash::make($validated['password'])
        ]);

        return response()->json([
            'message' => 'User created successfully.',
            'data' => new UserResource($user)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }

        return response()->json([
            'message' => 'User fetch successfully.',
            'data' => new UserResource($user)
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateDetails(UpdateDetailsUserRequest $request, $userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }

        $user->update($request->validated());

        return response()->json([
            'message' => 'User details updated successfully.',
            'data' => new UserResource($user)
        ], 200);
    }

    public function updatePassword(UpdatePasswordUserRequest $request, $userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }

        $validated = $request->validated();

        $user->update($request->validated() + [
            'password' => Hash::make($validated['password'])
        ]);

        return response()->json([
            'message' => 'User password updated successfully.',
            'data' => new UserResource($user)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json([
                'message' => 'User not found.',
            ], 404);
        }

        return response()->json([
            'message' => 'User deleted successfully.',
        ], 204);
    }
}
