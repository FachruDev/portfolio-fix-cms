<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait HasDisplayOrder
{
    protected function initializeHasDisplayOrder(): void
    {
        $this->attributes['order_no'] ??= 0;
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('order_no')->oldest('id');
    }
}
