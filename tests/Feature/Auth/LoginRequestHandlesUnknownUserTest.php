<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginRequestHandlesUnknownUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_unknown_user_login_request_does_not_throw_server_error(): void
    {
        $response = $this->post(route('login.store'), [
            'email' => 'unknown@example.com',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $response->assertRedirect();
    }
}
