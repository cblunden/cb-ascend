<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static BookBorrowBuilder query()
 */
class BookBorrow extends Model
{
    use HasFactory;

    public $casts = [
        'borrowed_at' => 'datetime',
        'returned_at' => 'datetime',
    ];
}
