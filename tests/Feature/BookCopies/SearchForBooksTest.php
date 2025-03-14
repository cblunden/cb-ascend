<?php

namespace Tests\Feature\BookCopies;

use App\Models\Author;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Library;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Pagination\LengthAwarePaginator;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class SearchForBooksTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function cannot_search_for_books_without_authentication()
    {
        $response = $this->get(route('book-copies.index'));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function can_search_for_books_by_name()
    {
        $library = Library::factory()->create();
        [$copyOne, $copyTwo] = BookCopy::factory()->count(2)->create(['library_id' => $library->id]);
        $user = User::factory()->hasAttached($library)->create();

        $response = $this->actingAs($user)->get(route('book-copies.index', [
            'name' => $copyOne->book->name
        ]));

        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('BookCopies/Index')
            ->count('book_copies.data', 1)
            ->has('book_copies', function (AssertableInertia $page) use ($copyOne) {
                $page->where('data.0.id', $copyOne->id);
            }));
    }

    /** @test */
    public function can_search_for_book_by_author()
    {
        $library = Library::factory()->create();
        $bookOne = Book::factory()->hasAttached(Author::factory()->create())->create();
        $bookTwo = Book::factory()->hasAttached(Author::factory()->create())->create();
        $copyOne = BookCopy::factory()->create(['book_id' => $bookOne->id, 'library_id' => $library->id]);
        $copyTwo = BookCopy::factory()->create(['book_id' => $bookTwo->id, 'library_id' => $library->id]);
        $user = User::factory()->hasAttached($library)->create();

        $response = $this->actingAs($user)->get(route('book-copies.index', [
            'author' => $copyOne->book->authors->first()->name
        ]));

        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('BookCopies/Index')
            ->count('book_copies.data', 1)
            ->has('book_copies', function (AssertableInertia $page) use ($copyOne) {
                $page->where('data.0.id', $copyOne->id);
            }));
    }
}
