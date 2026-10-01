<?php

namespace App\Interfaces;

interface ParticipantInteface
{
    // public function get(array $data): array;

    public function findByCiNumber($ciNumber);
}
