<?php

namespace App\Services\Aspiration;

use App\Models\Aspirations;
use App\Models\InputAspirations;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class AspirationQueryService
{
    public function buildAdminInputQuery(array $filters = [], ?string $sort = null): Builder
    {
        return InputAspirations::query()
            ->with(['student', 'category', 'aspiration'])
            ->filter($filters)
            ->when(!empty($filters['from']), function (Builder $query) use ($filters) {
                $query->where('created_at', '>=', Carbon::parse($filters['from'])->startOfDay());
            })
            ->when(!empty($filters['to']), function (Builder $query) use ($filters) {
                $query->where('created_at', '<=', Carbon::parse($filters['to'])->endOfDay());
            })
            ->with('aspiration', function ($query) {
                $query->withCount('votes');
            })
            ->when(($filters['mode'] ?? null) === 'custom', function (Builder $q) {
                $q->orderBy('is_kept')->orderBy('input_at')->orderBy('id_input');
            })
            ->when($sort === 'priority', function (Builder $q) {
                $q->orderByDesc(
                    Aspirations::selectRaw('count(*)')
                        ->from('votes')
                        ->whereColumn('votes.aspiration_id', 'aspirations.id_aspiration')
                        ->whereColumn('aspirations.id_input', 'input_aspirations.id_input')
                );
            }, function (Builder $q) use ($filters) {
                if (($filters['mode'] ?? null) === 'custom') {
                    return;
                }
                $q->latest();
            });
    }
}
