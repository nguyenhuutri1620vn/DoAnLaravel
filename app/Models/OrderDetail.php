<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;
    protected $table = 'order_detail';
    public $fillable = [
        'orderID',
        'productID',
        'count',
        'price'
    ];
    protected $with = ['product'];
    public function product(){
        return $this->belongsTo(Product::class, 'productID', 'id');
    }
}
