<?php

namespace App\Actions;

use App\Models\BookBorrow;
use App\Models\BookCopy;
use App\Models\BorrowRequest;
use Illuminate\Support\Facades\DB;
use App\Traits\UsesBookCopyFromRequest;

/**
 * @property int $book_copy_id
 */
class CompleteBorrow extends Action
{
    public function rules(): array
    {
        return [
            'book_copy_id' => ['required', 'numeric', function ($attribute, $value, $fail) {
                if (! $this->bookCopy()) {
                    return $fail(__('validation.exists', compact('attribute')));
                }
            }],
        ];
    }

    protected function run(): BookBorrow
    {
        // remove borrowed status and set back to available
        $this->bookCopy()->markAsAvailable();

        // update the borrow record to mark it as returned
        $borrow = BookBorrow::where('book_copy_id', $this->bookCopy()->getKey())
            ->where('user_id', $this->user()->id)
            ->whereNull('returned_at')
            ->limit(1)->first();

        $borrow->returned_at = now();
        $borrow->save();

        return $borrow;
    }

    private function bookCopy(): ?BookCopy
    {
        return once(fn () => BookCopy::query()
            ->where('id', $this->book_copy_id)
            ->whereAccessibleTo($this->user())
            ->whereHasActiveBorrow()
            ->first());
    }
}
