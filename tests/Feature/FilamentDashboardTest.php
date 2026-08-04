<?php

namespace Tests\Feature;

use App\Filament\Widgets\ContentReadiness;
use App\Filament\Widgets\LatestContactMessages;
use App\Filament\Widgets\RecentProjects;
use App\Models\User;
use Database\Seeders\PortfolioCmsPermissionSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class FilamentDashboardTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_super_admin_can_view_portfolio_cms_dashboard(): void
    {
        $this->seed(PortfolioCmsPermissionSeeder::class);

        $admin = User::where('email', 'fachru2006@gmail.com')->firstOrFail();

        $this->actingAs($admin)
            ->get('/admin')
            ->assertOk()
            ->assertSee('Portfolio CMS Overview')
            ->assertSee('Publishing Readiness')
            ->assertSee('Recent Projects');
    }

    public function test_dashboard_widgets_are_permission_aware(): void
    {
        $this->seed(PortfolioCmsPermissionSeeder::class);

        $admin = User::where('email', 'fachru2006@gmail.com')->firstOrFail();
        $this->actingAs($admin);

        $this->assertTrue(ContentReadiness::canView());
        $this->assertTrue(RecentProjects::canView());
        $this->assertTrue(LatestContactMessages::canView());
    }
}
