<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseTeacher extends Model
{
    // 指定資料表名稱
    protected $table = 'course_teacher';

    // 指定主鍵
    protected $primaryKey = 'teacher_id';

    // 關閉時間戳
    public $timestamps = false;

    // 允許批量賦值的欄位
    protected $fillable = [
        'teacher_img',
        'teacher_name',
        'teacher_phone',
        'teacher_email',
        'teacher_expertise',
        'teacher_intro',
        'teacher_status'
    ];
}
