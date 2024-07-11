<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class Article extends Model
{
    use HasFactory;

    // 指定表名
    protected $table = 'article';

    // 指定主鍵欄位
    protected $primaryKey = 'article_id';

    // 關閉時間戳
    public $timestamps = false;

    // 資料表的欄位
    protected $fillable = [
        'user_id_fk',
        'article_title',
        'article_content',
        'article_createtime',
        'article_edittime',
        'article_status',
        'article_category',
    ];

    // 日期欄位類型轉換
    protected $casts = [
        'article_createtime' => 'datetime',
        'article_edittime' => 'datetime',
    ];

    // 默認值
    protected $attributes = [
        'article_status' => 1,
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
