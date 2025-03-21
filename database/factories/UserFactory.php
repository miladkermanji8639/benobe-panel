<?php
namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
            'first_name'       => $this->faker->name,
            'email'      => $this->faker->unique()->safeEmail,
            'password'   => Hash::make('password'), // Default password
            'user_type'  => 0,                      // Assuming 0 is for patients
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
