<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class TwoFactorChallengeTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_public_two_factor_and_passkey_routes_are_closed(): void
    {
        $this->get('/two-factor-challenge')->assertNotFound();
        $this->post('/two-factor-challenge')->assertNotFound();
        $this->get('/passkeys/login/options')->assertNotFound();
        $this->post('/passkeys/login')->assertNotFound();
        $this->get('/user/two-factor-qr-code')->assertNotFound();
        $this->get('/user/two-factor-recovery-codes')->assertNotFound();
    }
}
