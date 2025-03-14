<?php

namespace Database\Factories;

use App\Models\BookCopy;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BorrowRequest>
 */
class BorrowRequestFactory extends Factory
{
    public function definition()
    {
        return [
            'book_copy_id' => fn () => BookCopy::factory()->create()->id,
            'user_id' => fn () => User::factory()->create()->id,
            'requested_at' => now()->toDateTimeString(),
            'requested_until' => now()->addDay()->toDateTimeString(),
        ];
    }
}
