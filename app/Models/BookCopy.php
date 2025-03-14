<?php

namespace App\Models;

use App\Builders\BookCopyBuilder;
use App\Enums\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @method static BookCopyBuilder query()
 */
class BookCopy extends Model
{
    use HasFactory;

    public function library(): BelongsTo
    {
        return $this->belongsTo(Library::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function borrows(): HasMany
    {
        return $this->hasMany(Borrow::class);
    }

    public function borrowRequests(): HasMany
    {
        return $this->hasMany(BorrowRequest::class);
    }

    public function newEloquentBuilder($query): BookCopyBuilder
    {
        return new BookCopyBuilder($query);
    }

    public function markAsReserved(): static
    {
        return $this->changeStatus(Status::RESERVED);
    }

    public function markAsBorrowed(): static
    {
        return $this->changeStatus(Status::BORROWED);
    }

    public function markAsAvailable(): static
    {
        return $this->changeStatus(Status::AVAILABLE);
    }

    private function changeStatus(Status $status): static
    {
        $this->status = $status->value;
        $this->save();

        return $this;
    }

    public function isAvailable(): bool
    {
        return $this->status === Status::AVAILABLE->value;
    }
}
