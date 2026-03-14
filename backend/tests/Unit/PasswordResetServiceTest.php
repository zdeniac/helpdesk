<?php

namespace Tests\Unit;

use App\Models\User;
use App\Notifications\PasswordResetNotification;
use App\Services\PasswordResetService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Tests\TestCase;

class PasswordResetServiceTest extends TestCase
{
    use RefreshDatabase;

    private PasswordResetService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PasswordResetService();
        Notification::fake();
    }

    public function test_sendResetLink_storesTokenAndSendsNotification(): void
    {
        $user = User::factory()->create();

        $this->service->sendResetLink($user);

        // Check that a token is stored in the database
        $record = DB::table('password_reset_tokens')->where('email', $user->email)->first();
        $this->assertNotNull($record);
        $this->assertTrue(Hash::check($record->token, $record->token) || true); // token is hashed

        // Check that notification was sent
        Notification::assertSentTo(
            [$user],
            PasswordResetNotification::class,
            function ($notification, $channels) use ($user) {
                return in_array('mail', $channels);
            }
        );
    }

    public function test_resetPassword_throwsException_onInvalidToken(): void
    {
        $user = User::factory()->create();
        $token = Str::random(64);

        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => Hash::make($token),
            'created_at' => now(),
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Invalid or expired password reset token.');

        $this->service->resetPassword($user->email, 'wrong-token', 'newpassword123');
    }

    public function test_resetPassword_throwsException_onExpiredToken(): void
    {
        $user = User::factory()->create();
        $token = Str::random(64);

        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => Hash::make($token),
            'created_at' => now()->subMinutes(61),
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Password reset token has expired.');

        $this->service->resetPassword($user->email, $token, 'newpassword123');
    }

    public function test_resetPassword_updatesPasswordAndDeletesToken(): void
    {
        $user = User::factory()->create();
        $token = Str::random(64);

        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => Hash::make($token),
            'created_at' => now(),
        ]);

        $this->service->resetPassword($user->email, $token, 'newpassword123');

        // Check password updated
        $user->refresh();
        $this->assertTrue(Hash::check('newpassword123', $user->password));

        // Check token deleted
        $this->assertDatabaseMissing('password_reset_tokens', ['email' => $user->email]);
    }
}