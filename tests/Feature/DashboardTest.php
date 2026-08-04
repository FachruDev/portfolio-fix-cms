<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_starter_dashboard_route_is_closed(): void
    {
        $this->get('/dashboard')->assertNotFound();
    }

    public function test_filament_dashboard_redirects_guests_to_admin_login(): void
    {
        $this->get('/admin')
            ->assertRedirect('/admin/login');
    }
}
