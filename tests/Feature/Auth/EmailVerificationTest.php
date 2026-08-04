<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_public_email_verification_routes_are_closed(): void
    {
        $this->get('/email/verify')->assertNotFound();
        $this->get('/email/verify/1/hash')->assertNotFound();
        $this->post('/email/verification-notification')->assertNotFound();
    }
}
