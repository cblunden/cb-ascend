<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Actions\CreateBorrow;
use App\Actions\CompleteBorrow;

class BookBorrowController extends Controller
{
    public function borrowBook(Request $request)
    {

        CreateBorrow::dispatch($request->only('book_copy_id'));

        return response()->json([
            'success' => true,
            'message' => 'Book successfully borrowed.'
        ]);
    }

    public function returnBook(Request $request)
    {
        CompleteBorrow::dispatch($request->only('book_copy_id'));

        return response()->json([
            'success' => true,
            'message' => 'Book successfully returned.'
        ]);
    }
}
