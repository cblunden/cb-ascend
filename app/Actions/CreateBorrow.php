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
class CreateBorrow extends Action
{
    use UsesBookCopyFromRequest;

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
        // mark the book as borrowed
        $this->bookCopy()->markAsBorrowed();

        // create new borrow record
        $borrow = new BookBorrow();
        $borrow->book_copy_id = $this->bookCopy()->getKey();
        $borrow->user_id = $this->user()->id;
        $borrow->borrowed_at = now();
        $borrow->save();

        return $borrow;
    }
}
