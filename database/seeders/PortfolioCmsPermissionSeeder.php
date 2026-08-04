<?php

namespace Database\Seeders;

use App\Models\AboutPage;
use App\Models\AboutPageSection;
use App\Models\AboutPageSectionItem;
use App\Models\CmsMediaAsset;
use App\Models\ContactInformation;
use App\Models\ContactMessage;
use App\Models\ExpertiseSection;
use App\Models\HobbyItem;
use App\Models\HobbySection;
use App\Models\LandingPage;
use App\Models\LandingPageAboutItem;
use App\Models\LandingPageAboutSection;
use App\Models\Note;
use App\Models\NotesCategory;
use App\Models\NotesHero;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\ProjectCoreFeature;
use App\Models\ProjectDesignHighlight;
use App\Models\ProjectHero;
use App\Models\SiteSetting;
use App\Models\SocialMediaLink;
use App\Models\TechStack;
use App\Models\Tool;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PortfolioCmsPermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $accessPanel = $this->createPermissions(['access admin panel'])->first();
        $contentPermissions = $this->createPermissionsFor($this->contentModels());
        $messagePermissions = $this->createPermissionsFor([ContactMessage::class]);
        $accessControlPermissions = $this->createPermissionsFor([Permission::class, Role::class]);

        $superAdmin = Role::findOrCreate('Super Admin', 'web');
        $contentEditor = Role::findOrCreate('Content Editor', 'web');
        $messageViewer = Role::findOrCreate('Message Viewer', 'web');

        $superAdmin->syncPermissions([
            $accessPanel,
            ...$contentPermissions,
            ...$messagePermissions,
            ...$accessControlPermissions,
        ]);

        $contentEditor->syncPermissions([
            $accessPanel,
            ...$contentPermissions,
        ]);

        $messageViewer->syncPermissions([
            $accessPanel,
            ...collect($messagePermissions)
                ->filter(fn (Permission $permission): bool => in_array($permission->name, [
                    'view-any ContactMessage',
                    'view ContactMessage',
                    'update ContactMessage',
                    'delete ContactMessage',
                    'delete-any ContactMessage',
                ], true))
                ->all(),
        ]);

        User::updateOrCreate(
            ['email' => 'fachru2006@gmail.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('admin123'),
            ],
        )->assignRole($superAdmin);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * @param  array<int, class-string>  $models
     * @return Collection<int, Permission>
     */
    private function createPermissionsFor(array $models): Collection
    {
        $names = collect($models)
            ->flatMap(fn (string $model): array => collect($this->permissionAffixes())
                ->map(fn (string $affix): string => $affix.' '.class_basename($model))
                ->all())
            ->unique()
            ->values();

        return $this->createPermissions($names->all());
    }

    /**
     * @param  array<int, string>  $names
     * @return Collection<int, Permission>
     */
    private function createPermissions(array $names): Collection
    {
        $names = collect($names)->unique()->values();
        $existing = Permission::query()
            ->where('guard_name', 'web')
            ->whereIn('name', $names)
            ->pluck('name')
            ->all();

        $missing = $names
            ->diff($existing)
            ->map(fn (string $name): array => [
                'name' => $name,
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ])
            ->all();

        if ($missing !== []) {
            Permission::query()->insert($missing);
        }

        return Permission::query()
            ->where('guard_name', 'web')
            ->whereIn('name', $names)
            ->get();
    }

    /**
     * @return array<int, class-string>
     */
    private function contentModels(): array
    {
        return [
            AboutPage::class,
            AboutPageSection::class,
            AboutPageSectionItem::class,
            CmsMediaAsset::class,
            ContactInformation::class,
            ExpertiseSection::class,
            HobbyItem::class,
            HobbySection::class,
            LandingPage::class,
            LandingPageAboutItem::class,
            LandingPageAboutSection::class,
            Note::class,
            NotesCategory::class,
            NotesHero::class,
            Project::class,
            ProjectCategory::class,
            ProjectCoreFeature::class,
            ProjectDesignHighlight::class,
            ProjectHero::class,
            SiteSetting::class,
            SocialMediaLink::class,
            TechStack::class,
            Tool::class,
        ];
    }

    /**
     * @return array<int, string>
     */
    private function permissionAffixes(): array
    {
        return [
            'view-any',
            'view',
            'create',
            'update',
            'delete',
            'delete-any',
            'replicate',
            'restore',
            'restore-any',
            'reorder',
            'force-delete',
            'force-delete-any',
        ];
    }
}
