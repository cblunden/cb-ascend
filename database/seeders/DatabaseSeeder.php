<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Actions\RegisterUser;
use App\Models\Author;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Library;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $libraries = Library::factory()->count(5)->create();

        $libraries->each(function ($library) {
            Collection::times(10, fn () => RegisterUser::dispatch([
                'name' => fake()->name,
                'email' => fake()->email,
                'password' => 'secret1234',
                'password_confirmation' => 'secret1234',
                'library_id' => $library->id
            ]));
        });

        RegisterUser::dispatch([
            'name' => 'Test User',
            'email' => 'test.user@test.com',
            'password' => 'secret1234',
            'password_confirmation' => 'secret1234',
            'library_id' => $libraries->first()->id
        ]);

        $books = Book::factory()->count(1000)->create();

        $books->each(function (Book $book) use ($libraries) {
            $authors = Author::factory()->count(fake()->numberBetween(1, 3))->create();
            $book->authors()->sync($authors);

            $libraries->each(function ($library) use ($book) {
                BookCopy::factory()->create([
                    'book_id' => $book->id,
                    'library_id' => $library->id
                ]);
            });
        });
    }
}
