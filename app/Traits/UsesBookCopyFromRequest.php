<?php

namespace App\Traits;

use App\Models\BookCopy;

trait UsesBookCopyFromRequest
{
    private function bookCopy(): ?BookCopy
    {
        return once(fn () => BookCopy::query()
            ->where('id', $this->book_copy_id)
            ->whereAccessibleTo($this->user())
            ->whereAvailable()
            ->first());
    }
}
