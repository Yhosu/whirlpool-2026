<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

final class MundialCoupon extends Model
{
    use HasFactory;

    protected $table = 'mundial_coupons';

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
        'category',
        // 'created_at', 'updated_at'
    ];

    protected $attributes = [
        'category' => 'legacy',
    ];

    public $timestamps = true;

    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'participant_id', 'id');
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
        return $this->hasOne(MundialCoupon::class, 'id', 'coupon_id');
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
