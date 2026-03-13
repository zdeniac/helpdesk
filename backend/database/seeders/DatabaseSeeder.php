<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@backend.com',
            'role' => UserRole::ADMIN->value,
            'password' => 'admin',
        ]);

        User::factory()->create([
            'name' => 'Helpdesk Agent',
            'email' => 'agent@backend.com',
            'role' => UserRole::AGENT->value,
            'password' => 'agent',
        ]);

        User::factory()
            ->count(10)
            ->hasEvents(3)
            ->create(['role' => UserRole::USER->value]);
    }
}
