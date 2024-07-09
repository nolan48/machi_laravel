<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    // 指定資料表名稱
    protected $table = 'course';

    // 指定主鍵
    protected $primaryKey = 'course_id';

    // 關閉時間戳
    public $timestamps = false;

    // 允許批量賦值的欄位
    protected $fillable = [
        'course_name',
        'course_description',
        'course_description_full',
        'course_category',
        'teacher_id_fk',
        'course_location',
        'course_price',
        'course_enroll_start',
        'course_enroll_end',
        'course_start_time',
        'course_end_time',
        'course_status',
        'course_teacher',
        'course_teacher_description'
    ];
}
