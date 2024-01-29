<?php

namespace Sergmoro1\Imageable\Database\Factories;

use Sergmoro1\Imageable\Models\User;
use Orchestra\Testbench\Factories\UserFactory as TestbenchUserFactory;
use Illuminate\Support\Str;

class UserFactory extends TestbenchUserFactory
{
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => $this->faker->password(),
            'remember_token' => Str::random(10),
        ];
    }
}
