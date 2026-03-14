<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\PasswordResetNotification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

final class PasswordResetService
{
    const TOKEN_EXPIRATION_MINUTES = 60;
    const TABLE = 'password_reset_tokens';

    public function sendResetLink(User $user): void
    {
        $this->storeToken($user, $token = Str::random(64));
        $user->notify(new PasswordResetNotification($token, $user->email));
    }

    /**
     * Reset user's password using a token.
     *
     * @throws \RuntimeException if token is invalid or expired
     */
    public function resetPassword(string $email, string $token, string $newPassword): void
    {
        $record = DB::table(self::TABLE)->where('email', $email)->first();

        if (!$record || !Hash::check($token, $record->token)) {
            throw new \RuntimeException('Invalid or expired password reset token.');
        }

        // Check expiration
        if (Carbon::parse($record->created_at)->addMinutes(self::TOKEN_EXPIRATION_MINUTES)->isPast()) {
            throw new \RuntimeException('Password reset token has expired.');
        }

        $this->updateUserPassword(User::where('email', $email)->firstOrFail(), $newPassword);

        // Delete token
        DB::table(self::TABLE)->where('email', $email)->delete();
    }

    private function storeToken(User $user, string $token): void
    {
        // Remove any previous token
        DB::table(self::TABLE)->where('email', $user->email)->delete();

        // Insert new hashed token
        DB::table(self::TABLE)->insert([
            'email' => $user->email,
            'token' => Hash::make($token),
            'created_at' => now(),
        ]);
    }

    private function updateUserPassword(User $user, string $newPassword): void
    {
        $user->password = Hash::make($newPassword);
        $user->save();
    }
}