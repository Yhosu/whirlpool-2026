<?php

namespace App\Interfaces;

interface ParticipantServiceInteface
{
    public function registerParticipant(array $data): array;

    public function findByCiNumber($ciNumber);

    public function assignRandomEcommercePrize(string $formId): array;
}

