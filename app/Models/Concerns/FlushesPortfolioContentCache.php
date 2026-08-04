<?php

namespace App\Models\Concerns;

use App\Services\PortfolioContentService;
use Illuminate\Database\Eloquent\Model;

trait FlushesPortfolioContentCache
{
    protected static function bootFlushesPortfolioContentCache(): void
    {
        static::saved(fn (Model $model): mixed => app(PortfolioContentService::class)->flush());
        static::deleted(fn (Model $model): mixed => app(PortfolioContentService::class)->flush());
    }
}
