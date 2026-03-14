<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\SendPasswordResetRequest;
use App\Models\User;
use App\Services\PasswordResetService;

class PasswordResetController extends Controller
{
    public function __construct(
        private readonly PasswordResetService $service
    ) {
    }

    public function sendResetLink(SendPasswordResetRequest $request)
    {
        $this->service->sendResetLink(
            User::where('email', $request->validated('email'))->firstOrFail()
        );

        return response()->json([
            'message' => 'Password reset link has been sent to your email.'
        ]);
    }

    public function reset(ResetPasswordRequest $request)
    {
        try {
            $this->service->resetPassword(
                $request->validated('email'),
                $request->validated('token'),
                $request->validated('password')
            );

            return response()->json([
                'message' => 'Password has been reset successfully.'
            ]);
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}
