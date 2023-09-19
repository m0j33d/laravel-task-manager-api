<?php

namespace Tests\Feature\Auth;

use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }


    public function test_user_with_valid_credential_can_login(): void
    {
        $data = [
            'email' => 'test@example.com',
            'password' => 'password'
        ];

        $request = $this->post(route('login'), $data);

        $request->assertJsonFragment(['status' => true]);
    }

    public function test_new_users_with_invalid_credentials_cannot_register(): void
    {
        $data = [
            'email' => fake()->freeEmail(),
            'password' => 'lol'
        ];

        $request = $this->post(route('login'), $data);

        $request->assertUnprocessable();
        $request->assertJsonFragment(['status' => false]);
    }

    public function test_users_with_invalid_data_cannot_login(): void
    {
        $data = [
            'email' => fake()->freeEmail(),
            'password' => null
        ];

        $request = $this->post(route('login'), $data);

        $request->assertUnprocessable();
        $request->assertJsonFragment(['status' => false]);
    }

}
