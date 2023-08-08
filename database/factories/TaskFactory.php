<?php

namespace Database\Factories;

use App\Models\TaskList;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'task_list_id' => TaskList::factory(),
            'title' => $this->faker->word(),
            'date_time' => now()->format('Y-m-d H:i:s'),
            'description' => $this->faker->word(),
            'completed' => false,
        ];
    }
}
