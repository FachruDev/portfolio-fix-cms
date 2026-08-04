<?php

namespace App\Providers;

use App\Models\AboutPage;
use App\Models\AboutPageSection;
use App\Models\AboutPageSectionItem;
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
use App\Policies\CmsPolicy;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        $this->configureAuthorization();
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }

    protected function configureAuthorization(): void
    {
        Gate::before(fn (User $user): ?bool => $user->isSuperAdmin() ? true : null);

        foreach ($this->cmsPolicyModels() as $model) {
            Gate::policy($model, CmsPolicy::class);
        }
    }

    /**
     * @return array<class-string>
     */
    protected function cmsPolicyModels(): array
    {
        return [
            AboutPage::class,
            AboutPageSection::class,
            AboutPageSectionItem::class,
            ContactInformation::class,
            ContactMessage::class,
            ExpertiseSection::class,
            HobbyItem::class,
            HobbySection::class,
            LandingPage::class,
            LandingPageAboutItem::class,
            LandingPageAboutSection::class,
            Note::class,
            NotesCategory::class,
            NotesHero::class,
            Permission::class,
            Project::class,
            ProjectCategory::class,
            ProjectCoreFeature::class,
            ProjectDesignHighlight::class,
            ProjectHero::class,
            Role::class,
            SiteSetting::class,
            SocialMediaLink::class,
            TechStack::class,
            Tool::class,
        ];
    }
}
