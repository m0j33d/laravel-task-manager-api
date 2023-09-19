<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        // Make a POST request to the logout endpoint
        $response = $this->getJson(route('logout'));

        // Assert that the access token has been deleted
        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $user->id,
        ]);

        // Assert the response
        $response->assertStatus(200)
            ->assertJsonFragment(['status' => true]);

    }

    public function test_new_users_with_invalid_data_cannot_register(): void
    {
        $request = $this->get(route('logout'));

        $request->assertUnauthorized();
    }
}
