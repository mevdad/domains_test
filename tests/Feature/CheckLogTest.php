<?php

namespace Tests\Feature;

use App\Models\Domain;
use App\Models\DomainCheck;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_logs_page_requires_auth(): void
    {
        $this->get(route('logs.index'))->assertRedirect(route('login'));
    }

    public function test_user_can_view_their_check_logs(): void
    {
        $user = User::factory()->create();
        $domain = Domain::factory()->create(['user_id' => $user->id]);
        $check = DomainCheck::factory()->create(['domain_id' => $domain->id]);

        Domain::factory()->create(); // another user's domain — must not appear

        $this->actingAs($user)
            ->get(route('logs.index'))
            ->assertOk()
            ->assertInertia(
                fn ($page) => $page
                    ->component('logs/index')
                    ->has('checks.data', 1)
                    ->where('checks.data.0.id', $check->id)
            );
    }

    public function test_logs_include_notification_status(): void
    {
        $user = User::factory()->create([
            'notification_settings' => [
                'telegram' => ['enabled' => true, 'bot_token' => 'token', 'chat_id' => '123'],
            ],
        ]);
        $domain = Domain::factory()->create(['user_id' => $user->id]);
        DomainCheck::factory()->create(['domain_id' => $domain->id]);

        $this->actingAs($user)
            ->get(route('logs.index'))
            ->assertOk()
            ->assertInertia(
                fn ($page) => $page
                    ->component('logs/index')
                    ->has('enabledChannels')
                    ->has('checks.data.0.notifications')
            );
    }

    public function test_logs_only_show_users_own_checks(): void
    {
        $user = User::factory()->create();
        $otherDomain = Domain::factory()->create();
        DomainCheck::factory()->create(['domain_id' => $otherDomain->id]);

        $this->actingAs($user)
            ->get(route('logs.index'))
            ->assertOk()
            ->assertInertia(
                fn ($page) => $page
                    ->component('logs/index')
                    ->has('checks.data', 0)
            );
    }
}
