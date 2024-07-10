<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $table = 'order_item';
    protected $primaryKey = 'order_item_id';
    public $timestamps = false; // 如果你不需要 timestamps，保持 false

    protected $fillable = [
        'order_id_fk',
        'order_product_type',
        'order_product_id',
        'order_product_name',
        'order_product_detail',
        'order_product_count',
        'order_product_price',
    ];

    // 定義關聯
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id_fk', 'order_id');
    }
}
