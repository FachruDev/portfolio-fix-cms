<?php

namespace Tests\Feature;

use App\Filament\Resources\CmsMediaAssets\CmsMediaAssetResource;
use App\Models\CmsMediaAsset;
use App\Models\User;
use Database\Seeders\PortfolioCmsPermissionSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CmsMediaAssetTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_media_asset_stores_public_url_and_file_metadata(): void
    {
        config([
            'filesystems.media_disk' => 'r2',
            'filesystems.disks.r2.url' => 'https://r2.example.test',
        ]);

        Storage::fake('r2', [
            'url' => 'https://r2.example.test',
        ]);
        Storage::disk('r2')->put('media-library/general/sample.txt', 'sample media file');

        $asset = CmsMediaAsset::create([
            'title' => 'Sample File',
            'collection' => 'general',
            'file_path' => 'media-library/general/sample.txt',
            'alt_text' => 'Sample file',
        ]);

        $this->assertSame('r2', $asset->disk);
        $this->assertSame('https://r2.example.test/media-library/general/sample.txt', $asset->public_url);
        $this->assertSame('sample.txt', $asset->file_name);
        $this->assertSame(17, $asset->size);
        $this->assertTrue($asset->is_active);
    }

    public function test_content_editor_can_manage_media_library(): void
    {
        $this->seed(PortfolioCmsPermissionSeeder::class);

        $editor = User::factory()->create();
        $editor->assignRole('Content Editor');

        $this->actingAs($editor);

        $this->assertTrue(CmsMediaAssetResource::canViewAny());
        $this->assertTrue(CmsMediaAssetResource::canCreate());
    }
}
