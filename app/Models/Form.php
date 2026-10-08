<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

final class Form extends Model
{
    use HasFactory;

    protected $table = 'forms';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $casts = ['id' => 'string'];

    protected $fillable = [
        'id',
        'participant_id',
        'name',
        'ci_number',
        'cellphone',
        'invoice_url',
        'invoice_image',
        'business_unit',
        'city',
        'code',
        'status',
        'sale_id',
        'branch_id',
        'prize_id',
        'user_id',
        'invalidation_reason',
        'is_scratched',
        'voucher',
        'observations',
        'type',
        'control_code'
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

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class, 'sale_id');
    }

    public function prize(): BelongsTo
    {
        return $this->belongsTo(Prize::class, 'prize_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'participant_id');
    }

    public function getInvoiceViewUrlAttribute(): ?string
    {
        // 1. Si tiene invoice_url (ej. SIAT QR o URL directa en S3)
        if (! empty($this->invoice_url)) {
            return $this->invoice_url;
        }

        // 2. Si tiene invoice_image en S3 o local
        if (! empty($this->invoice_image)) {
            if (str_starts_with($this->invoice_image, 'http')) {
                return $this->invoice_image;
            }

            if (config('filesystems.disks.s3.bucket')) {
                return \Illuminate\Support\Facades\Storage::disk('s3')->url($this->invoice_image);
            }

            return asset('storage/'.$this->invoice_image);
        }

        return null;
    }
}

