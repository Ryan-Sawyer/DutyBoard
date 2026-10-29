<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** A truck: an ambulance, an engine, a squad. */
#[Fillable(['station_id', 'name', 'description'])]
class Unit extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return ['out_of_service_at' => 'datetime'];
    }

    /** @return BelongsTo<Station, $this> */
    public function station(): BelongsTo
    {
        return $this->belongsTo(Station::class);
    }

    public function isInService(): bool
    {
        return $this->out_of_service_at === null;
    }

    /** @param Builder<Unit> $query */
    public function scopeInService(Builder $query): void
    {
        $query->whereNull('out_of_service_at');
    }
}
