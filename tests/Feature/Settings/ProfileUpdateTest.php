<?php

namespace Tests\Feature\Settings;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class ProfileUpdateTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_public_profile_settings_routes_are_closed(): void
    {
        $this->get('/settings')->assertNotFound();
        $this->get('/settings/profile')->assertNotFound();
    }
}
