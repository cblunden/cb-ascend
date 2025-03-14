<?php

namespace Database\Factories;

use App\Enums\Status;
use App\Models\Book;
use App\Models\Library;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BookCopy>
 */
class BookCopyFactory extends Factory
{
    public function definition()
    {
        return [
            'barcode' => Str::uuid()->toString(),
            'book_id' => fn () => Book::factory()->create()->id,
            'library_id' => fn () => Library::factory()->create()->id,
            'status' => Status::AVAILABLE->value,
            'aisle' => collect(range('A', 'Z'))->random(),
            'shelf' => collect(range('A', 'D'))->random(),
            'number' => collect(range(1, 500))->random(),
        ];
    }
}
