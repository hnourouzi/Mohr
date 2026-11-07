<?php

namespace Tests\Feature\Api;

use App\Enums\ContentStatus;
use App\Jobs\ModerateContentJob;
use App\Models\Content;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\RateLimiter;
use Laravel\Sanctum\PersonalAccessToken;
use Tests\TestCase;

class ApiV1Test extends TestCase
{
    use RefreshDatabase;

    private function bearer(User $user): array
    {
        $token = $user->createToken('test')->plainTextToken;

        return ['Authorization' => 'Bearer '.$token];
    }

    public function test_register_login_logout_and_me(): void
    {
        $register = $this->postJson('/api/v1/auth/register', [
            'name' => 'کاربر جدید',
            'email' => 'new-api@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $register->assertCreated()
            ->assertJsonStructure(['user', 'token', 'token_type', 'expires_at']);

        $login = $this->postJson('/api/v1/auth/login', [
            'email' => 'new-api@example.com',
            'password' => 'password',
            'device_name' => 'phpunit',
        ]);

        $login->assertOk()
            ->assertJsonPath('user.email', 'new-api@example.com');

        $token = $login->json('token');

        $me = $this->getJson('/api/v1/auth/me', [
            'Authorization' => 'Bearer '.$token,
        ]);

        $me->assertOk()
            ->assertJsonPath('wallet.balance', 0);

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/auth/logout')
            ->assertOk();

        $this->assertNull(PersonalAccessToken::findToken($token));

        auth()->forgetGuards();

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/auth/me')
            ->assertUnauthorized()
            ->assertJsonPath('code', 'unauthenticated');
    }

    public function test_protected_routes_require_token(): void
    {
        $this->getJson('/api/v1/auth/me')
            ->assertUnauthorized()
            ->assertJsonPath('code', 'unauthenticated');
    }

    public function test_submitting_content_debits_wallet_and_dispatches_job(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $user->wallet->update(['balance' => 5000]);

        $response = $this->postJson('/api/v1/contents', [
            'body' => 'متن آزمایشی API',
            'channel' => 'web',
        ], $this->bearer($user));

        $response->assertCreated()
            ->assertJsonPath('data.status', ContentStatus::Pending->value);

        $this->assertSame(4000, $user->wallet->fresh()->balance);
        Queue::assertPushed(ModerateContentJob::class);
    }

    public function test_insufficient_balance_returns_402(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $user->wallet->update(['balance' => 100]);

        $this->postJson('/api/v1/contents', [
            'body' => 'متن آزمایشی API',
        ], $this->bearer($user))
            ->assertStatus(402)
            ->assertJsonPath('code', 'insufficient_balance');

        Queue::assertNothingPushed();
    }

    public function test_user_cannot_view_others_content(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $content = Content::factory()->create(['user_id' => $owner->id]);

        $this->getJson("/api/v1/contents/{$content->id}", $this->bearer($other))
            ->assertForbidden()
            ->assertJsonPath('code', 'forbidden');
    }

    public function test_retry_rules(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $failed = Content::factory()->create([
            'user_id' => $user->id,
            'status' => ContentStatus::Failed,
            'retry_count' => 0,
        ]);

        $this->postJson("/api/v1/contents/{$failed->id}/retry", [], $this->bearer($user))
            ->assertStatus(202);

        $approved = Content::factory()->create([
            'user_id' => $user->id,
            'status' => ContentStatus::Approved,
        ]);

        $this->postJson("/api/v1/contents/{$approved->id}/retry", [], $this->bearer($user))
            ->assertStatus(409)
            ->assertJsonPath('code', 'not_retryable');
    }

    public function test_non_admin_cannot_access_admin_endpoints(): void
    {
        $user = User::factory()->create();

        $this->getJson('/api/v1/admin/users', $this->bearer($user))
            ->assertForbidden()
            ->assertJsonPath('code', 'forbidden');
    }

    public function test_admin_can_list_users(): void
    {
        $admin = User::factory()->admin()->create();

        $this->getJson('/api/v1/admin/users', $this->bearer($admin))
            ->assertOk();
    }

    public function test_login_is_throttled_per_email_and_ip(): void
    {
        RateLimiter::clear('api-login');

        $payload = [
            'email' => 'throttle@example.com',
            'password' => 'wrong-password',
        ];

        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/v1/auth/login', $payload)->assertStatus(422);
        }

        $this->postJson('/api/v1/auth/login', $payload)
            ->assertStatus(429)
            ->assertJsonPath('code', 'too_many_requests');
    }

    public function test_login_rejects_invalid_credentials_with_generic_message(): void
    {
        User::factory()->create([
            'email' => 'exists@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'exists@example.com',
            'password' => 'not-the-password',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }
}
