<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Password yang di-cache agar tidak di-hash ulang setiap pemanggilan.
     */
    protected static ?string $password;

    /**
     * State default untuk user baru.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'              => fake('id_ID')->name(),
            'email'             => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password'          => static::$password ??= Hash::make('password'),
            'role'              => 'staff', // Default role adalah staff
            'remember_token'    => Str::random(10),
        ];
    }

    // ─────────────────────────────────────────────────────────────────
    // State Methods — Penggunaan ekspresif di Seeder
    // ─────────────────────────────────────────────────────────────────

    /**
     * Set role user menjadi 'admin'.
     *
     * Contoh: User::factory()->admin()->create()
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'admin',
        ]);
    }

    /**
     * Set role user menjadi 'staff'.
     *
     * Contoh: User::factory()->staff()->create()
     */
    public function staff(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'staff',
        ]);
    }

    /**
     * Buat user dengan email yang belum diverifikasi.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
