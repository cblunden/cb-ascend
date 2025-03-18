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


class CreateBorrowTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function cannot_borrow_a_book_without_authentication()
    {
        $library = Library::factory()->create();
        $bookCopy = BookCopy::factory()->create(['library_id' => $library->id]);

        $response = $this->post(route('borrow.create'), [
            'book_copy_id' => $bookCopy->id
        ]);
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function cannot_borrow_a_book_from_different_library()
    {
        $libraryOne = Library::factory()->create();
        $libraryTwo = Library::factory()->create();
        $bookCopy = BookCopy::factory()->create(['library_id' => $libraryOne->id]);
        $user = User::factory()->hasAttached($libraryTwo)->create();

        $response = $this->actingAs($user)->post(route('borrow.create'), [
            'book_copy_id' => $bookCopy->id
        ]);

        $response->assertSessionHas('errors');
        $this->assertDatabaseEmpty('borrow_requests');
    }

    /** @test */
    public function cannot_borrow_book_that_is_not_available()
    {
        $library = Library::factory()->create();
        $bookCopy = BookCopy::factory()->create([
            'status' => Status::RESERVED->value,
            'library_id' => $library->id
        ]);
        $user = User::factory()->hasAttached($library)->create();

        $response = $this->actingAs($user)->post(route('borrow.create'), [
            'book_copy_id' => $bookCopy->id
        ]);

        $response->assertSessionHasErrors('book_copy_id');
        $this->assertDatabaseEmpty('borrow_requests');
    }

    /** @test */
    public function can_borrow_a_book()
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

        $response = $this->actingAs($user)->post(route('borrow.create'), [
            'book_copy_id' => $bookCopyTwo->id
        ]);

        $response->assertJson(['success' => true, 'message' => 'Book successfully borrowed.']);

        // check that the BookBorrow record has been created correctly
        $this->assertCount(1, $borrows = BookBorrow::all());
        $this->assertEquals($user->id, $borrows->first()->user_id);
        $this->assertEquals($bookCopyTwo->id, $borrows->first()->book_copy_id);
        $this->assertEquals(now()->toDateTimeString(), $borrows->first()->borrowed_at->toDateTimeString());

        // check that the book copy status has been updated
        $this->assertEquals(Status::BORROWED->value, BookCopy::find($bookCopyTwo->id)->status);
    }
}
