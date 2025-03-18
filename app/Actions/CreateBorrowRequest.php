<?php

namespace App\Actions;

use App\Models\BookCopy;
use App\Models\BorrowRequest;
use Illuminate\Support\Facades\DB;
use App\Traits\UsesBookCopyFromRequest;

/**
 * @property int $book_copy_id
 */
class CreateBorrowRequest extends Action
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

    protected function run(): BorrowRequest
    {
        $this->bookCopy()->markAsReserved();

        $borrowRequest = new BorrowRequest();
        $borrowRequest->book_copy_id = $this->bookCopy()->getKey();
        $borrowRequest->user_id = $this->user()->id;
        $borrowRequest->requested_at = now();
        $borrowRequest->requested_until = now()->addDay();
        $borrowRequest->save();

        return $borrowRequest;
    }
}
