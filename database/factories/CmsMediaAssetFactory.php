<?php

namespace Database\Factories;

use App\Models\CmsMediaAsset;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CmsMediaAsset>
 */
class CmsMediaAssetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->words(2, true),
            'collection' => 'general',
            'disk' => 'public',
            'file_path' => 'media-library/general/'.fake()->uuid().'.jpg',
            'file_name' => fake()->uuid().'.jpg',
            'mime_type' => 'image/jpeg',
            'size' => fake()->numberBetween(10_000, 900_000),
            'width' => 1200,
            'height' => 800,
            'alt_text' => fake()->sentence(3),
            'caption' => fake()->sentence(),
            'is_active' => true,
        ];
    }
}
