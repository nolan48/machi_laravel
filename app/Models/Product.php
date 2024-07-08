<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    // 指定資料表名稱
    protected $table = 'product';

    // 不使用時間戳
    public $timestamps = false;

    // 批量賦值的欄位
    protected $fillable = [
        'product_id',
        'product_name',
        'product_description',
        'product_description_full',
        'product_category',
        'category_id_fk',
        'category_sub_id_fk',
        'product_subtitle_small',
        'product_subtitle_middle',
        'product_subtitle_large',
        'product_price_small',
        'product_price_middle',
        'product_price_large',
        'product_count',
        'product_createtime',
        'product_status',
    ];

    // 自動增量主鍵
    protected $primaryKey = 'product_id';

    // 欄位的類型轉換
    protected $casts = [
        'product_createtime' => 'datetime',
    ];

    // 軟刪除使用的日期欄位


    // 預設屬性的值
    protected $attributes = [
        'product_count' => 300,
        'product_status' => 1,
    ];

    // 自動增量主鍵不自動添加的欄位
    public $incrementing = true;

    // 自動增加的主鍵類型
    protected $keyType = 'int';


}
