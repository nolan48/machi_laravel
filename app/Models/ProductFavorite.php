<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductFavorite extends Model
{
    use HasFactory;

    // 指定資料表名稱
    protected $table = 'product_favorite';

    // 不使用時間戳
    public $timestamps = false;

    // 批量賦值的欄位
    protected $fillable = [
        'product_favorite_id',
        'user_id_fk',
        'product_id_fk',
    ];

    // 自動增量主鍵
    protected $primaryKey = 'product_favorite_id';

    // 自動增量主鍵不自動添加的欄位
    public $incrementing = true;

    // 自動增加的主鍵類型
    protected $keyType = 'int';
}