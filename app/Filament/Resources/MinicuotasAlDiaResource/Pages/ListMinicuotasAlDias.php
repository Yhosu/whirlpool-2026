<?php

namespace App\Filament\Resources\MinicuotasAlDiaResource\Pages;

use App\Filament\Resources\MinicuotasAlDiaResource;
use App\Models\Participant;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListMinicuotasAlDias extends ListRecords
{
    protected static string $resource = MinicuotasAlDiaResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function applySearchToTableQuery(Builder $query): Builder
    {
        $search = $this->getTableSearch();

        if (empty($search)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($search) {
            if (preg_match('/[0-9]/', $search)) {
                if (is_numeric($search)) {
                    // 1. Purely numeric (CI, Cellphone)
                    $participantIds = Participant::where('ci_number', 'like', "{$search}%")
                        ->orWhere('cellphone', 'like', "{$search}%")
                        ->limit(200)
                        ->pluck('id')
                        ->toArray();

                    if (! empty($participantIds)) {
                        $q->whereIn('mundial_coupons.participant_id', $participantIds);
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                } else {
                    // 2. Alphanumeric (Coupon code candidate)
                    $q->where('mundial_coupons.code', 'like', "{$search}%");
                }
            } else {
                // 3. Purely alphabetic/spaces (Name, City, or Coupon code prefix)
                $words = array_filter(explode(' ', $search));

                $participantQuery = Participant::query();
                if (! empty($words)) {
                    $participantQuery->where(function (Builder $subQ) use ($words) {
                        foreach ($words as $word) {
                            $subQ->where('name', 'like', "%{$word}%");
                        }
                    });
                }

                $participantIds = $participantQuery
                    ->orWhere('city', 'like', "%{$search}%")
                    ->limit(200)
                    ->pluck('id')
                    ->toArray();

                $q->where('mundial_coupons.code', 'like', "{$search}%");

                if (! empty($participantIds)) {
                    $q->orWhereIn('mundial_coupons.participant_id', $participantIds);
                }
            }
        });
    }
}
