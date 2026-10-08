<?php

namespace App\Interfaces;

use App\Models\Form;

interface ParticipantServiceInteface
{
    public function registerParticipant(array $data): array;

    public function findByCiNumber($ciNumber);

    public function assignRandomEcommercePrize(string $formId): array;

    public function assignRandomPrizeByForm(Form $form): array;

    public function checkCodeAttempts(string $code): array;

    public function markFormAsUsed(string $formId, ?string $controlCode = null): array;

    public function registerInformalMarketForm(array $data): Form;
}



