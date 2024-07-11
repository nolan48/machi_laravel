<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Order extends Model
{
    use HasFactory;

    protected $table = 'order';
    protected $primaryKey = 'order_id';

    public $timestamps = true;

    protected $fillable = [
        'user_id_fk',
        'order_payment',
        'order_username',
        'order_address',
        'order_phone',
        'order_amount',
        'order_total',
        'order_status',
    ];

    const CREATED_AT = 'order_createtime';
    const UPDATED_AT = null;

    protected $dates = [
        'order_createtime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->{static::CREATED_AT} = Carbon::now()->addHours(8);
        });
    }
}
