<?php

namespace Api\BookBorrows;

use App\Enums\Status;
use App\Models\BookCopy;
use App\Models\BorrowRequest;
use App\Models\Library;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use App\Models\BookBorrow;


class CompleteBorrowTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function cannot_return_a_book_without_authentication()
    {
        $library = Library::factory()->create();
        $bookCopy = BookCopy::factory()->create(['library_id' => $library->id]);

        $response = $this->patch(route('borrow.complete'), [
            'book_copy_id' => $bookCopy->id
        ]);
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function cannot_return_book_that_has_not_been_borrowed()
    {
        $library = Library::factory()->create();
        $bookCopy = BookCopy::factory()->create([
            'status' => Status::AVAILABLE->value,
            'library_id' => $library->id
        ]);
        $user = User::factory()->hasAttached($library)->create();

        $response = $this->actingAs($user)->patch(route('borrow.complete'), [
            'book_copy_id' => $bookCopy->id
        ]);

        $response->assertSessionHasErrors('book_copy_id');

        // check that the book_copy status has not been updated
        $this->assertEquals(STATUS::AVAILABLE->value, BookCopy::find($bookCopy->id)->status);
    }

    /** @test */
    public function can_return_a_book()
    {
        $library = Library::factory()->create();
        $libraryTwo = Library::factory()->create();

        $bookCopy = BookCopy::factory()->create([
            'library_id' => $library->id,
        ]);

        $bookCopyTwo = BookCopy::factory()->create([
            'library_id' => $libraryTwo->id,
        ]);

        $user = User::factory()
            ->hasAttached($library)
            ->hasAttached($libraryTwo)
            ->create();

        // create a borrow record
        $borrow = new BookBorrow();
        $borrow->book_copy_id = $bookCopyTwo->id;
        $borrow->user_id = $user->id;
        $borrow->borrowed_at = now()->subDay();
        $borrow->save();

        // mark the book copy as borrowed
        $bookCopyTwo->markAsBorrowed();

        // return the book copy
        $response = $this->actingAs($user)->patch(route('borrow.complete'), [
            'book_copy_id' => $bookCopyTwo->id
        ]);

        // assert the response
        $response->assertJson(['success' => true, 'message' => 'Book successfully returned.']);

        // check that the book_borrow record has been updated
        $this->assertCount(1, $borrows = BookBorrow::all());
        $this->assertEquals($user->id, $borrows->first()->user_id);
        $this->assertEquals($bookCopyTwo->id, $borrows->first()->book_copy_id);
        $this->assertEquals(now()->toDateTimeString(), $borrows->first()->returned_at->toDateTimeString());

        // check that the book_copy status has been updated
        $this->assertEquals(Status::AVAILABLE->value, BookCopy::find($bookCopyTwo->id)->status);
    }
}
