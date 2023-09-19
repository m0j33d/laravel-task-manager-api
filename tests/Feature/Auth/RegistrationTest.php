<?php

namespace Tests\Feature\Auth;

use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }


    public function test_new_users_can_register(): void
    {
        $data = [
            'first_name' => 'Joe',
            'last_name' => 'Graphik',
            'email' => fake()->freeEmail(),
            'password' => 'Password1@'
        ];

        $request = $this->post(route('register'), $data);

        $request->assertJsonFragment(['status' => true]);
    }

    public function test_new_users_with_invalid_data_cannot_register(): void
    {
        $data = [
            'first_name' => 'Joe',
            'last_name' => 'Graphik',
            'email' => 'lol',
            'password' => 'lol'
        ];

        $request = $this->post(route('register'), $data);

        $request->assertUnprocessable();
        $request->assertJsonFragment(['status' => false]);
    }

    public function test_new_users_get_verification_email(): void
    {
        Event::fake();
        $data = [
            'first_name' => 'Joe',
            'last_name' => 'Graphika',
            'email' => fake()->freeEmail(),
            'password' => 'Password1@'
        ];

        $request = $this->post(route('register'), $data);

        $request->assertJsonFragment(['status' => true]);
        Event::assertDispatched(Registered::class);
    }
}
