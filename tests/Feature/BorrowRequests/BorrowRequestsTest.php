<?php

namespace Tests\Feature\BorrowRequests;

use App\Enums\Status;
use App\Models\BookCopy;
use App\Models\BorrowRequest;
use App\Models\Library;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class BorrowRequestsTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function cannot_request_to_borrow_a_book_without_authentication()
    {
        $library = Library::factory()->create();
        $bookCopy = BookCopy::factory()->create(['library_id' => $library->id]);

        $response = $this->post(route('borrow-requests.store'), [
            'book_copy_id' => $bookCopy->id
        ]);
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function cannot_request_to_borrow_a_book_from_different_library()
    {
        $libraryOne = Library::factory()->create();
        $libraryTwo = Library::factory()->create();
        $bookCopy = BookCopy::factory()->create(['library_id' => $libraryOne->id]);
        $user = User::factory()->hasAttached($libraryTwo)->create();

        $response = $this->actingAs($user)->post(route('borrow-requests.store'), [
            'book_copy_id' => $bookCopy->id
        ]);

        $response->assertSessionHas('errors');
        $this->assertDatabaseEmpty('borrow_requests');
    }

    /** @test */
    public function cannot_request_to_borrow_book_that_is_not_available()
    {
        $library = Library::factory()->create();
        $bookCopy = BookCopy::factory()->create([
            'status' => Status::RESERVED->value,
            'library_id' => $library->id
        ]);
        $user = User::factory()->hasAttached($library)->create();

        $response = $this->actingAs($user)->post(route('borrow-requests.store'), [
            'book_copy_id' => $bookCopy->id
        ]);

        $response->assertSessionHasErrors('book_copy_id');
        $this->assertDatabaseEmpty('borrow_requests');
    }

    /** @test */
    public function can_request_to_borrow_a_book()
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

        $response = $this->actingAs($user)->post(route('borrow-requests.store'), [
            'book_copy_id' => $bookCopyTwo->id
        ]);

        $response->assertSessionHas('success');
        $this->assertCount(1, $borrowRequests = BorrowRequest::all());
        $this->assertEquals($user->id, $borrowRequests->first()->user_id);
        $this->assertEquals($bookCopyTwo->id, $borrowRequests->first()->book_copy_id);
        $this->assertEquals(now()->toDateTimeString(), $borrowRequests->first()->requested_at->toDateTimeString());
        $this->assertEquals(now()->addDay()->toDateTimeString(), $borrowRequests->first()->requested_until->toDateTimeString());
    }
}
