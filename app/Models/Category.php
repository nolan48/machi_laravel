<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Category extends Model
{


    // 定义表名
    protected $table = 'category';

    // 定义主键字段
    protected $primaryKey = 'category_id';

    // 关闭 Laravel 自动管理的时间戳字段
    public $timestamps = false;

    // 允许批量赋值的字段
    protected $fillable = [
        'category_name',
        'category_status',
    ];

    // 字段的默认值
    protected $attributes = [
        'category_status' => 1,
    ];


}
