<?php

namespace App\Models;

use App\Models\Concerns\FlushesPortfolioContentCache;
use Database\Factories\CmsMediaAssetFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

#[Fillable(['title', 'collection', 'disk', 'file_path', 'file_name', 'mime_type', 'size', 'width', 'height', 'alt_text', 'caption', 'is_active'])]
class CmsMediaAsset extends Model
{
    /** @use HasFactory<CmsMediaAssetFactory> */
    use FlushesPortfolioContentCache, HasFactory;

    protected $attributes = [
        'collection' => 'general',
        'disk' => 'public',
        'is_active' => true,
    ];

    protected $appends = [
        'public_url',
        'formatted_size',
        'is_image',
    ];

    protected static function booted(): void
    {
        static::saving(function (CmsMediaAsset $asset): void {
            $asset->syncFileMetadata();
        });
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function getPublicUrlAttribute(): ?string
    {
        return filled($this->file_path) ? Storage::disk($this->disk)->url($this->file_path) : null;
    }

    public function getFormattedSizeAttribute(): ?string
    {
        if (! $this->size) {
            return null;
        }

        if ($this->size >= 1048576) {
            return number_format($this->size / 1048576, 2).' MB';
        }

        return number_format($this->size / 1024, 1).' KB';
    }

    public function getIsImageAttribute(): bool
    {
        return filled($this->mime_type) && str_starts_with($this->mime_type, 'image/');
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'size' => 'integer',
            'width' => 'integer',
            'height' => 'integer',
        ];
    }

    private function syncFileMetadata(): void
    {
        if (blank($this->file_path) || ! Storage::disk($this->disk)->exists($this->file_path)) {
            return;
        }

        $disk = Storage::disk($this->disk);
        $this->file_name = $this->file_name ?: basename($this->file_path);
        $this->mime_type = $disk->mimeType($this->file_path) ?: $this->mime_type;
        $this->size = $disk->size($this->file_path) ?: $this->size;

        if (! $this->mime_type || ! str_starts_with($this->mime_type, 'image/') || $this->mime_type === 'image/svg+xml') {
            return;
        }

        $dimensions = @getimagesizefromstring($disk->get($this->file_path));

        if ($dimensions !== false) {
            $this->width = $dimensions[0];
            $this->height = $dimensions[1];
        }
    }
}
