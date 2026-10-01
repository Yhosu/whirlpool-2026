<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

final class PrizeStockHistory extends Model
{
    use HasFactory;

    protected $table = 'prize_stock_history';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $casts = [
        'id' => 'string',
        'quantity' => 'integer',
    ];

    protected $fillable = [
        'id',
        'prize_id',
        'branch_id',
        'quantity',
        'type',
        'form_id',
        'user_id',
        'description',
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

        self::created(function ($history) {
            $prizeBranch = PrizeBranch::firstOrCreate([
                'prize_id' => $history->prize_id,
                'branch_id' => $history->branch_id,
            ]);

            $quantity = $history->quantity;

            if ($history->type === 'subtraction') {
                $quantity = -abs($quantity);
            } elseif ($history->type === 'addition') {
                $quantity = abs($quantity);
            }

            $prizeBranch->increment('stock', $quantity);
        });
    }

    public function prize(): BelongsTo
    {
        return $this->belongsTo(Prize::class, 'prize_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class, 'form_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
