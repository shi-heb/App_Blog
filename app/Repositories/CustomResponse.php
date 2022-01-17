<?php
namespace App\Repositories;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CustomResponse
{
    public static function respondWithToken($token, $user)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'user' => $user
        ],200);
    }






















}
