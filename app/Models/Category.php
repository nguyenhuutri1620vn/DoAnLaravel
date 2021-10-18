<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Category extends Model
{
    use HasFactory, HasApiTokens;
    public $table = 'category';
    public $fillable = [
        'meta_title',
        'meta_keyword',
        'meta_descrip',
        'slug',
        'name',
        'description',
        'image',
        'status',
    ];
}
