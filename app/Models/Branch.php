<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

final class Branch extends Model
{
    use HasFactory;

    protected $table = 'branchs';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $casts = ['id' => 'string'];

    protected $fillable = [
        'id',
        'name',
        'address',
        'schedule',
        'city',
        'channel',
    ];

    public $timestamps = true;

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = Str::uuid();
            }
        });
    }

    public function prizes(): BelongsToMany
    {
        return $this->belongsToMany(Prize::class, 'prize_branch', 'branch_id', 'prize_id')
            ->withPivot('stock')
            ->withTimestamps();
    }
}
