<?php

namespace App\Builders;

use App\Enums\Status;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class BookCopyBuilder extends Builder
{
    public function whereAccessibleTo(User $user): static
    {
        return $this->whereHas('library', fn ($library) => $library
            ->whereIn('id', $user->libraries->isEmpty() ? [-1] : $user->libraries->pluck('id')));
    }

    public function applySearchFiltersFrom(Request $request): static
    {
        if ($request->input('name') || $request->input('author')) {
            $this->whereHas('book', fn (Builder $book) => $book
                ->when($request->input('name'), fn ($book, $name) => $book->where('name', 'LIKE', "%{$name}%"))
                ->when($request->input('author'), fn ($book, $author) => $book
                    ->whereHas('authors', fn ($authors) => $authors->where('name', 'LIKE', "%{$author}%"))));
        }

        return $this;
    }

    public function whereNotReserved(): static
    {
        return $this->whereStatusNot(Status::RESERVED);
    }

    public function whereReserved(): static
    {
        return $this->whereStatus(Status::RESERVED);
    }

    public function whereAvailable(): static
    {
        return $this->whereStatus(Status::AVAILABLE);
    }

    public function whereStatus(Status $status): static
    {
        return $this->where('status', $status->value);
    }

    public function whereStatusNot(Status $status): static
    {
        return $this->where('status', '!=', $status->value);
    }

    public function whereHasNoActiveReservation(): static
    {
        return $this->whereDoesntHave('borrowRequests', fn (Builder $borrowRequests) => $borrowRequests
            ->where('requested_until', '>', now()->toDateTimeString()));
    }

    public function whereHasNoActiveBorrow(): static
    {
        return $this->whereDoesntHave('borrows', fn (Builder $borrowRequests) => $borrowRequests
            ->whereNull('returned_at'));
    }

    public function whereHasActiveBorrow(): static
    {
        return $this->whereHas('borrows', fn (Builder $borrowRequests) => $borrowRequests
            ->whereNull('returned_at'));
    }
}
