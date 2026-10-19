<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $email = config('admin.email');
        $password = config('admin.password');

        if (! is_string($email) || $email === '' || ! is_string($password) || $password === '') {
            return;
        }

        $admin = User::query()->firstOrNew(['email' => $email]);
        $admin->name = config('admin.name', 'Administrator');
        $admin->password = Hash::make($password);
        $admin->email_verified_at = now();
        $admin->role = UserRole::Admin->value;
        $admin->save();
    }
}
