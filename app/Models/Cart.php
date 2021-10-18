<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;
    protected $table = "cart";
    protected $fillable = [
        'userID',
        'productID',
        'quantity'
    ];

    protected $with = ['product'];
    public function product(){
        return $this->belongsTo(Product::class, 'productID', 'id');
    }
}
