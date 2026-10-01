<?php

namespace App\Interfaces;

interface ParticipantServiceInteface
{
    // public function get(array $data): array;

    public function registerParticipant(array $data): array;

    public function findByCiNumber($ciNumber);
}
