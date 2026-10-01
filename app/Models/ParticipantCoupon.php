<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

final class ParticipantCoupon extends Model
{
    use HasFactory;

    protected $table = 'participant_coupons';

    public $incrementing = false;

    protected $hidden = ['updated_at'];

    protected $casts = ['id' => 'string', 'active' => 'boolean'];

    protected $with = [];

    protected $keyType = 'string';

    protected $fillable = [
        'sale_id',
        'coupon_id',
        'participant_id',
        'code',
        'status',
        'active',
        'price_id',
        'business_unit',
        // 'created_at', 'updated_at'
    ];

    public $timestamps = true;

    public function participant(): HasOne
    {
        return $this->hasOne(Participant::class, 'id', 'participant_id');
    }

    public function prize(): HasOne
    {
        return $this->hasOne(Prize::class, 'id', 'prize_id');
    }

    public function sale(): HasOne
    {
        return $this->hasOne(Sale::class, 'id', 'sale_id')->select('id', 'sale_type', 'control_code', 'sale_date', 'invoice_url', 'invoice_number');
    }

    public function coupon(): HasOne
    {
        return $this->hasOne(Coupon::class, 'id', 'coupon_id');
    }

    protected $es = 'Cupón por participante';

    protected $en = 'Cupón por participante';

    public function getLang($lang)
    {
        return $this->$lang;
    }

    public static $rules_edit = [
        'id' => 'required',
    ];

    public function getSaleTypeAttribute()
    {
        return $this->sale->sale_type ?? null;
    }
}
