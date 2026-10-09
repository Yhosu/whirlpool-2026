<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

final class Code extends Model
{
    use HasFactory;

    protected $table = 'codes';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $casts = [
        'id' => 'string',
        'prize_id' => 'string',
        'form_id' => 'string',
        'value' => 'decimal:2',
        'days_valid' => 'integer',
        'is_used' => 'boolean',
        'expiration_date' => 'datetime',
        'used_at' => 'datetime',
    ];

    protected $fillable = [
        'id',
        'prize_id',
        'code',
        'product_code',
        'value',
        'expiration_date',
        'days_valid',
        'created_by',
        'status',
        'is_used',
        'used_at',
        'form_id',
    ];

    public $timestamps = true;

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function prize(): BelongsTo
    {
        return $this->belongsTo(Prize::class, 'prize_id');
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class, 'form_id');
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_used', false)->where('status', 'available');
    }

    public function scopeUsed($query)
    {
        return $query->where('is_used', true)->orWhere('status', 'used');
    }
}
