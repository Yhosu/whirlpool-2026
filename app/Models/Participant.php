<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

final class Participant extends Model
{
    use HasFactory;

    protected $table = 'participants';

    public $incrementing = false;

    protected $casts = ['id' => 'string'];

    protected $keyType = 'string';

    protected $with = [];

    protected $fillable = [
        'id',
        'name',
        'cellphone',
        'birth_date',
        'ci_number',
        'ci_number_complement',
        'ci_number_expedition',
        'city',
        'status',
        'email',
        'date',
        'time',
        'document_type',
    ];

    public $timestamps = true;

    protected $hidden = [];

    public function coupons(): HasMany
    {
        return $this->hasMany(ParticipantCoupon::class, 'participant_id', 'id');
    }

    public function created_coupons(): HasMany
    {
        return $this->hasMany(ParticipantCoupon::class, 'participant_id', 'id')->where('status', 'exchanged');
    }

    public function exchanged_coupons(): HasMany
    {
        return $this->hasMany(ParticipantCoupon::class, 'participant_id', 'id')->where('status', 'exchanged');
    }

    public function mundial_coupons(): HasMany
    {
        return $this->hasMany(MundialCoupon::class, 'participant_id', 'id');
    }

    public function forms(): HasMany
    {
        return $this->hasMany(Form::class, 'participant_id', 'id');
    }

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->id = \Str::uuid();
        });
    }

    protected $es = 'Participante';

    protected $en = 'Participante';

    public function getLang($lang)
    {
        return $this->$lang;
    }

    public static $rules_edit = [
        'id' => 'required',
    ];

    public static $rules_create = [
        'name' => 'required',
        'ci_number' => 'required',
    ];

    public function winner_coupon(): HasOne
    {
        return $this->hasOne(ParticipantCoupon::class, 'participant_id', 'id')->where('status', 'exchanged');
    }
}
