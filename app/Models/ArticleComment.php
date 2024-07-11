<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class ArticleComment extends Model
{
    use HasFactory;

    // 指定表名
    protected $table = 'article_comment';

    // 指定主鍵欄位
    protected $primaryKey = 'article_comment_id';

    // 關閉時間戳
    public $timestamps = false;

    // 資料表的欄位
    protected $fillable = [
        'article_id_fk',
        'user_id_fk',
        'article_comment_content',
        'article_comment_createtime',
        'article_comment_status',
    ];

    // 日期欄位類型轉換
    protected $casts = [
        'article_comment_createtime' => 'datetime',
    ];

    // 默認值
    protected $attributes = [
        'article_comment_status' => 1,
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
