<?php

namespace Tests\Support;

use App\Models\User;

/** Pembuat akun peran di dalam test, supaya test tidak bergantung pada seeder. */
trait AuthAsRole
{
    protected function actingAsAdmin(): User
    {
        $user = User::create([
            'name' => 'Admin Uji', 'email' => 'admin.uji@temji.test',
            'role' => User::ROLE_ADMIN, 'password' => 'password123',
        ]);
        $this->actingAs($user);

        return $user;
    }

    protected function actingAsWarga(): User
    {
        $user = User::create([
            'name' => 'Warga Uji', 'email' => 'warga.uji@temji.test',
            'role' => User::ROLE_WARGA, 'password' => 'password123',
        ]);
        $this->actingAs($user);

        return $user;
    }
}
