<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $table = 'cart_item';
    protected $primaryKey = 'cart_item_id';
    public $timestamps = false; // 如果你不需要 timestamps，保持 false

    protected $fillable = [
        'user_id_fk',
        'product_id_fk',
        'product_name',
        'product_subtitle',
        'product_price',
        'product_count',
        'course_address',
        'course_id_fk',
        'course_name',
        'course_price',
        'course_count',
        'course_date',
        'custom_size',
        'custom_layer',
        'custom_flavor',
        'custom_decor',
        'custom_price',
        'custom_count',
        'custom_img',
    ];
}
