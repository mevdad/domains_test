<?php

namespace Tests\Feature;

use App\Jobs\CheckDomain;
use App\Jobs\SendDomainNotification;
use App\Models\Domain;
use App\Models\DomainCheck;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class DomainCheckTest extends TestCase
{
    use RefreshDatabase;

    public function test_history_page_requires_auth(): void
    {
        $domain = Domain::factory()->create();
        $this->get(route('domains.checks.index', $domain))->assertRedirect(route('login'));
    }

    public function test_user_can_view_check_history_for_their_domain(): void
    {
        $user = User::factory()->create();
        $domain = Domain::factory()->create(['user_id' => $user->id]);
        $check = DomainCheck::factory()->create(['domain_id' => $domain->id]);

        $this->actingAs($user)
            ->get(route('domains.checks.index', $domain))
            ->assertOk()
            ->assertInertia(
                fn ($page) => $page
                    ->component('domains/checks')
                    ->has('checks.data', 1)
                    ->where('checks.data.0.id', $check->id)
            );
    }

    public function test_user_cannot_view_history_for_another_users_domain(): void
    {
        $user = User::factory()->create();
        $domain = Domain::factory()->create();

        $this->actingAs($user)
            ->get(route('domains.checks.index', $domain))
            ->assertForbidden();
    }

    public function test_check_domain_job_saves_successful_result(): void
    {
        Http::fake(['*' => Http::response('ok', 200)]);
        Queue::fake([SendDomainNotification::class]);

        $domain = Domain::factory()->create(['check_method' => 'GET', 'check_timeout' => 10]);
        DomainCheck::factory()->create(['domain_id' => $domain->id, 'is_up' => false]);

        (new CheckDomain($domain))->handle();

        $this->assertDatabaseHas('domain_checks', [
            'domain_id' => $domain->id,
            'is_up' => true,
            'method' => 'GET',
            'status_code' => 200,
            'response_body' => 'ok',
        ]);
    }

    public function test_check_domain_job_saves_failed_result(): void
    {
        Http::fake(['*' => Http::response('error', 500)]);
        Queue::fake([SendDomainNotification::class]);

        $domain = Domain::factory()->create(['check_method' => 'GET', 'check_timeout' => 10]);
        DomainCheck::factory()->create(['domain_id' => $domain->id, 'is_up' => true]);

        (new CheckDomain($domain))->handle();

        $this->assertDatabaseHas('domain_checks', [
            'domain_id' => $domain->id,
            'is_up' => false,
            'status_code' => 500,
        ]);
    }

    public function test_notification_records_created_on_status_change(): void
    {
        Http::fake(['*' => Http::response('ok', 200)]);
        Queue::fake([SendDomainNotification::class]);

        $user = User::factory()->create([
            'notification_settings' => ['mail' => ['enabled' => true]],
        ]);
        $domain = Domain::factory()->create(['user_id' => $user->id]);
        DomainCheck::factory()->create(['domain_id' => $domain->id, 'is_up' => false]);

        (new CheckDomain($domain))->handle();

        $latestCheck = DomainCheck::where('domain_id', $domain->id)->latest('checked_at')->first();

        $this->assertDatabaseHas('domain_check_notifications', [
            'domain_check_id' => $latestCheck->id,
            'channel' => 'mail',
            'status' => 'pending',
        ]);

        Queue::assertPushed(SendDomainNotification::class);
    }

    public function test_notification_records_created_when_status_unchanged(): void
    {
        Http::fake(['*' => Http::response('ok', 200)]);
        Queue::fake([SendDomainNotification::class]);

        $user = User::factory()->create([
            'notification_settings' => ['mail' => ['enabled' => true]],
        ]);
        $domain = Domain::factory()->create(['user_id' => $user->id]);
        DomainCheck::factory()->create(['domain_id' => $domain->id, 'is_up' => true]);

        (new CheckDomain($domain))->handle();

        $latestCheck = DomainCheck::where('domain_id', $domain->id)->latest('checked_at')->first();

        $this->assertDatabaseHas('domain_check_notifications', [
            'domain_check_id' => $latestCheck->id,
            'channel' => 'mail',
            'status' => 'pending',
        ]);

        Queue::assertPushed(SendDomainNotification::class);
    }

    public function test_telegram_notification_record_not_created_when_not_configured(): void
    {
        Http::fake(['*' => Http::response('ok', 200)]);
        Queue::fake([SendDomainNotification::class]);

        $user = User::factory()->create([
            'notification_settings' => [
                'telegram' => ['enabled' => true, 'bot_token' => null, 'chat_id' => null],
            ],
        ]);
        $domain = Domain::factory()->create(['user_id' => $user->id]);
        DomainCheck::factory()->create(['domain_id' => $domain->id, 'is_up' => false]);

        (new CheckDomain($domain))->handle();

        $latestCheck = DomainCheck::where('domain_id', $domain->id)->latest('checked_at')->first();

        $this->assertDatabaseMissing('domain_check_notifications', [
            'domain_check_id' => $latestCheck->id,
            'channel' => 'telegram',
        ]);
    }
}
