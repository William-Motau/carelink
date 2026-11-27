<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CookieConsentTest extends TestCase
{
    use RefreshDatabase;
    public function test_banner_hidden_when_consent_cookie_present(): void
    {
        $this->withCookie('cookie_consent', 'true')
            ->get('/')
            ->assertOk()
            ->assertDontSee('We value your privacy');
    }

    public function test_banner_shown_without_consent_cookie(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('We value your privacy');
    }
}