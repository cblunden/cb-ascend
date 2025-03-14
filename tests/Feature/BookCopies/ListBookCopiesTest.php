<?php

namespace Tests\Feature\BookCopies;

use App\Models\BookCopy;
use App\Models\Library;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class ListBookCopiesTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function can_list_book_copies_associated_with_users_library()
    {
        $library = Library::factory()->create();
        $libraryTwo = Library::factory()->create();

        BookCopy::factory()->count(2)->create(['library_id' => $library->id]);

        // Create some book copies for a different library that the user should not be able to see
        BookCopy::factory()->count(2)->create(['library_id' => $libraryTwo->id]);
        $user = User::factory()->hasAttached($library)->create();
        $response = $this->actingAs($user)->get(route('book-copies.index'));

        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('BookCopies/Index')
            ->count('book_copies.data', 2));
    }
}
