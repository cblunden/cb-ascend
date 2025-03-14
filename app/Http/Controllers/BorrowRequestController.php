<?php

namespace App\Http\Controllers;

use App\Actions\CreateBorrowRequest;
use Illuminate\Http\Request;

class BorrowRequestController extends Controller
{
    public function store(Request $request)
    {
        CreateBorrowRequest::dispatch($request->only('book_copy_id'));

        return to_route('book-copies.index')
            ->with('success', __('Successfully reserved the book copy!'));
    }
}
