<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Masuk sebagai akun Petugas K3RS untuk pengujian
     */
    protected function actingAsPetugas(?array $attributes = []): User
    {
        $user = User::factory()->petugas()->create($attributes);
        $this->actingAs($user);

        return $user;
    }

    /**
     * Masuk sebagai akun Kepala K3RS (Admin) untuk pengujian
     */
    protected function actingAsAdmin(?array $attributes = []): User
    {
        $user = User::factory()->admin()->create($attributes);
        $this->actingAs($user);

        return $user;
    }
}
