<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

final class Coupon extends Model
{
    use HasFactory;

    protected $table = 'coupons';

    public $incrementing = false;

    protected $casts = ['id' => 'string'];

    protected $with = ['price'];

    protected $fillable = [
        'id',
        'code',
        'status',
        'price_id',
    ];

    public $timestamps = true;

    protected $hidden = [];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->id = \Str::uuid();
        });
    }

    protected $es = 'Cupón';

    protected $en = 'Cupón';

    public function getLang($lang)
    {
        return $this->$lang;
    }

    public function price(): HasOne
    {
        return $this->hasOne(Price::class, 'id', 'price_id');
    }

    public function getNameAttribute()
    {
        return $this->coupon->code ?? '';
    }
}
