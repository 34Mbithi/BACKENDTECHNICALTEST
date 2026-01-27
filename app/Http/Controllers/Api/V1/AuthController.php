<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Auth\LoginAction;
use App\Data\LoginData;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuthController
{
    /**
     * Login endpoint.
     */
    public function login(Request $request, LoginAction $loginAction)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $data = LoginData::from($validated);
        $result = $loginAction->execute($data);

        return response()->json([
            'data' => $result,
        ], Response::HTTP_OK);
    }

    /**
     * Logout endpoint.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ], Response::HTTP_OK);
    }

    /**
     * Get current user profile.
     */
    public function me(Request $request)
    {
        return response()->json([
            'data' => new UserResource($request->user()),
        ], Response::HTTP_OK);
    }
}
