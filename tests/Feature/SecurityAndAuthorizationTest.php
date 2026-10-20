<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Content;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class SecurityAndAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_debug_test_route_is_not_registered(): void
    {
        $this->assertFalse(Route::has('test'));
        $this->get('/test')->assertNotFound();
    }

    public function test_user_cannot_self_top_up_wallet(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/panel/wallet/top-up', ['amount' => 5000])
            ->assertNotFound();
    }

    public function test_registration_cannot_escalate_role(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'مهاجم',
            'email' => 'attacker@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => UserRole::Admin->value,
        ]);

        $response->assertSessionHasErrors('role');

        $this->assertDatabaseMissing('users', [
            'email' => 'attacker@example.com',
            'role' => UserRole::Admin->value,
        ]);
    }

    public function test_user_cannot_view_others_content(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();

        $content = Content::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($other)
            ->get(route('panel.contents.show', $content))
            ->assertForbidden();
    }

    public function test_non_admin_cannot_access_admin_panel(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }
}
