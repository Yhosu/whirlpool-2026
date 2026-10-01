<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

final class Prize extends Model
{
    use HasFactory;

    protected $table = 'prizes';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $casts = [
        'id' => 'string',
        'active' => 'boolean',
        'amount' => 'decimal:2',
    ];

    protected $fillable = [
        'id',
        'name',
        'description',
        'amount',
        'active',
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

    public function branches(): BelongsToMany
    {
        return $this->belongsToMany(Branch::class, 'prize_branch', 'prize_id', 'branch_id')
            ->withPivot('stock')
            ->withTimestamps();
    }

    public function stockHistory(): HasMany
    {
        return $this->hasMany(PrizeStockHistory::class, 'prize_id');
    }

    protected $es = 'Premio';

    protected $en = 'Premio';

    public function getLang($lang)
    {
        return $this->$lang;
    }
}
