<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Sale extends Model
{
    use HasFactory;

    protected $table = 'sales';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $casts = ['id' => 'string'];

    protected $with = [];

    protected $fillable = [
        'participant_id',
        'name',
        'control_code',
        'business_unit',
        'sale_date',
        'quantity_items',
        'total_amount',
        'invoice_url',
        'city_agency',
        'invoice_number',
        'sale_type',
        'active',
    ];

    public $timestamps = true;

    protected $hidden = [];

    public function sale_items(): HasMany
    {
        return $this->hasMany(SaleItem::class, 'sale_id', 'id');
    }

    public function valid_sale_items(): HasMany
    {
        return $this->hasMany(SaleItem::class, 'sale_id', 'id')
            ->whereIn('code_brand', [2])
            ->where(function ($query) {
                $query->whereNull('category')
                    ->orWhereNotIn('category', [123, 129, 135]);
            })
            ->whereRaw('(amount / IFNULL(NULLIF(quantity, 0), 1)) >= 100');
    }

    public function participant()
    {
        return $this->belongsTo(Participant::class, 'participant_id', 'id');
    }

    public function coupons()
    {
        return $this->hasMany(ParticipantCoupon::class, 'sale_id', 'id');
    }

    public function forms(): HasMany
    {
        return $this->hasMany(Form::class, 'sale_id', 'id');
    }

    public function mundial_coupons(): HasMany
    {
        return $this->hasMany(MundialCoupon::class, 'sale_id', 'id');
    }

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->id = \Str::uuid();
        });
    }

    protected $es = 'Venta';

    protected $en = 'Venta';

    public function getLang($lang)
    {
        return $this->$lang;
    }
}
