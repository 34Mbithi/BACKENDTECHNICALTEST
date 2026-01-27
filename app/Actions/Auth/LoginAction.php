<?php

namespace App\Actions\Auth;

use App\Data\LoginData;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;

class LoginAction
{
    /**
     * Login a user and return API token.
     */
    public function execute(LoginData $data): array
    {
        $user = User::where('email', $data->email)->first();

        if (!$user || !\Illuminate\Support\Facades\Hash::check($data->password, $user->password)) {
            throw new AuthenticationException('Invalid credentials.');
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return [
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ];
    }
}
