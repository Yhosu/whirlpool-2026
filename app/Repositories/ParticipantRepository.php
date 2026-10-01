<?php

namespace App\Repositories;

use App\Interfaces\ParticipantInteface;
use App\Models\Participant;

final class ParticipantRepository implements ParticipantInteface
{
    public function findByCiNumber($ciNumber)
    {
        return Participant::with(['coupons', 'created_coupons', 'exchanged_coupons'])->where('ci_number', $ciNumber)->first();
    }
}
