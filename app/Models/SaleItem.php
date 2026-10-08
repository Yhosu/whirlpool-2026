<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

final class SaleItem extends Model
{
    use HasFactory;

    protected $table = 'sale_items';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $casts = ['id' => 'string'];

    protected $with = [];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    protected $fillable = [
        'sale_id',
        'name',
        'amount',
        'code_brand',
        'name_brand',
        'quantity',
    ];

    public $timestamps = true;

    protected $hidden = [];

    public function sale(): HasOne
    {
        return $this->hasOne(Sale::class, 'id', 'sale_id');
    }

    protected $es = 'Item de Venta';

    protected $en = 'Item de Venta';

    public function getLang($lang)
    {
        return $this->$lang;
    }
}
