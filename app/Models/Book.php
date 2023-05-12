<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static where(string $string, mixed $id)
 */
class Book extends Model
{
    use HasFactory;

    public $fillable = [
        'title',
        'author',
        'publication_date',
        'isbn',
        'genre',
    ];
}
