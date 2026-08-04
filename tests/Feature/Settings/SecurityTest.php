<?php

namespace Tests\Feature\Settings;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_public_security_settings_routes_are_closed(): void
    {
        $this->get('/settings/security')->assertNotFound();
        $this->get('/settings/appearance')->assertNotFound();
        $this->get('/.well-known/passkey-endpoints')->assertNotFound();
    }
}
