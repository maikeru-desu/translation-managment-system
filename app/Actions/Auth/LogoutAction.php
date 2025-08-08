<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use Illuminate\Http\Request;

final class LogoutAction
{
    /**
     * Handle the logout action.
     */
    public function handle(Request $request): array
    {
        $request->user()->currentAccessToken()->delete();

        return [
            'message' => 'Successfully logged out',
        ];
    }
}
