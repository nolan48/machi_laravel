<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'order';
    protected $primaryKey = 'order_id';
    public $timestamps = false; // 如果你不需要 timestamps，保持 false

    protected $fillable = [
        'user_id_fk',
        'order_payment',
        'order_username',
        'order_address',
        'order_phone',
        'order_amount',
        'order_total',
        'order_status',
        'order_createtime',
    ];

    protected $dates = [
        'order_createtime',
    ];
}
