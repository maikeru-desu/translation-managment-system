<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\Auth\LoginAction;
use App\Actions\Auth\LogoutAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class AuthController extends Controller
{
    /**
     * Login user and create token
     */
    public function login(LoginRequest $request, LoginAction $action): JsonResponse
    {
        $result = $action->handle($request->validated());

        return response()->json([
            'data' => [
                'user' => $result['user'],
                'token' => $result['token'],
            ],
            'message' => 'Successfully logged in',
        ], 200);
    }

    /**
     * Logout user (revoke token)
     */
    public function logout(Request $request, LogoutAction $action): JsonResponse
    {
        $result = $action->handle($request);

        return response()->json([
            'message' => $result['message'],
        ], 200);
    }
}
