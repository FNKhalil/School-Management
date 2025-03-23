<?php

namespace Database\Factories;

use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Mark>
 */
class MarkFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
            'student_id' => User::factory()->student(),
            'subject_id' => Subject::factory(),
            'teacher_id' => User::factory()->teacher(),
            'mark' => $this->faker->numberBetween(40, 100),
        ];
    }
}
