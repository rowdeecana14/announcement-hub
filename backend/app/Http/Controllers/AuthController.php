<?php

namespace App\Http\Controllers;

use App\Enums\User\Active;
use App\Enums\User\Statuses;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Auth\RegisterAuthRequest;
use App\Http\Requests\Auth\LoginAuthRequest;
use App\Http\Requests\Auth\UpdateDetailsRequest;
use App\Http\Requests\Auth\UpdatePasswordRequest;
use App\Http\Resources\User\ProfileUserResource;
use App\Http\Resources\User\UserResource;
use App\Enums\Auth\Messages as AuthMessages;
use App\Enums\User\Messages as UserMessages;


class AuthController extends Controller
{
    public function register(RegisterAuthRequest $request)
    {
        $validated = $request->validated();

        $user = User::create($validated + [
            'password' => Hash::make($validated['password'])
        ]);

        $token = $user->createToken('myapptoken')->plainTextToken;

        return response()->json([
            'message' => AuthMessages::TOKEN_GENERATED,
            'token' => $token
        ], 201);
    }

    public function login(LoginAuthRequest $request)
    {
        $credentials = $request->validated();
        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json(
                [
                    'message' => AuthMessages::INVALID_CREDENTIALS
                ],
                401
            );
        }

        if ($user->active === Active::NO->value) {
            return response()->json(
                [
                    'message' => UserMessages::USER_INACTIVE
                ],
                401
            );
        }

        if ($user->status !== Statuses::APPROVED->value) {
            return response()->json(
                [
                    'message' => UserMessages::USER_RESTRICTED
                ],
                401
            );
        }

        $token = $user->createToken('myapptoken')->plainTextToken;

        return response()->json([
            'message' => AuthMessages::LOGIN_SUCCESS,
            'token' => $token,
            'data' => new UserResource($user)
        ], 200);
    }

    public function profile()
    {
        $auth = Auth::user();

        return response()->json([
            'message' => UserMessages::PROFILE_FETCH,
            'data' => new ProfileUserResource($auth)
        ], 200);
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        $validated = $request->validated();

        $auth = auth()->user();
        $auth->update($request->validated() + [
            'password' => Hash::make($validated['password'])
        ]);

        return response()->json([
            'message' => AuthMessages::PASSWORD_UPDATED,
            'data' => new UserResource($auth->fresh())
        ], 200);
    }

    public function updateDetails(UpdateDetailsRequest $request)
    {
        $auth = auth()->user();
        $auth->update($request->validated());

        return response()->json([
            'message' => UserMessages::UPDATED_DETAILS,
            'data' => new UserResource($auth->fresh())
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => AuthMessages::LOGOUT_SUCCESS
        ],  200);
    }
}
