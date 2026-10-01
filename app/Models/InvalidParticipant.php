<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class InvalidParticipant extends Model
{
    use HasFactory;

    protected $table = 'invalid_participants';

    public $incrementing = false;

    protected $casts = ['id' => 'string'];

    protected $with = [];

    protected $fillable = [
    ];

    public $timestamps = true;

    protected $hidden = [];

    protected $es = 'Lista negra';

    protected $en = 'Lista negra';

    public function getLang($lang)
    {
        return $this->$lang;
    }
}
