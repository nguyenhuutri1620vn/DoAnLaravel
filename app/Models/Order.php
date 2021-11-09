<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $table = 'order';
    public $fillabe = [
        'userID',
        'paymentID',
        'payment_mode',
        'tracking_no',
        'number',
        'total_price',
        'provinceID',
        'districtID',
        'address',
        'remark',
        'note',
        'status',
    ];

    public function orderitem(){
        return $this->hasMany(OrderDetail::class, 'orderID', 'id');
    }

    protected $with = ['users'];
    public function users(){
        return $this->belongsTo(Users::class, 'userID', 'id');
    }
}
