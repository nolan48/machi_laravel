<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ArticleFavorite extends Model
{
    use HasFactory;

    // 指定表名
    protected $table = 'article_favorite';

    // 指定主鍵欄位
    protected $primaryKey = 'article_favorite_id';

    // 關閉時間戳
    public $timestamps = false;

    // 資料表的欄位
    protected $fillable = [
        'user_id_fk',
        'article_id_fk',
    ];

    // 如果需要underscored欄位名稱的話，可以使用覆寫的方法
    public function getAttribute($key)
    {
        $key = Str::snake($key);
        return parent::getAttribute($key);
    }

    public function setAttribute($key, $value)
    {
        $key = Str::snake($key);
        return parent::setAttribute($key, $value);
    }
}
