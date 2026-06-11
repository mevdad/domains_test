<?php

namespace Tests\Feature;

use App\Contracts\DomainNotificationChannel;
use App\Jobs\SendDomainNotification;
use App\Models\Domain;
use App\Models\DomainCheck;
use App\Models\DomainCheckNotification;
use App\Models\User;
use App\Notifications\Channels\MailDomainChannel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class SendDomainNotificationTest extends TestCase
{
    use RefreshDatabase;

    private function makeNotificationRecord(string $channel, array $userAttrs = []): DomainCheckNotification
    {
        $user = User::factory()->create(array_merge([
            'notification_settings' => [
                'telegram' => ['enabled' => true, 'bot_token' => 'fake-token', 'chat_id' => '123'],
            ],
        ], $userAttrs));
        $domain = Domain::factory()->create(['user_id' => $user->id]);
        $check = DomainCheck::factory()->create(['domain_id' => $domain->id, 'is_up' => false]);

        return DomainCheckNotification::create([
            'domain_check_id' => $check->id,
            'channel' => $channel,
            'status' => 'pending',
            'attempts' => 0,
        ]);
    }

    private function noopMailChannel(): DomainNotificationChannel
    {
        return new class implements DomainNotificationChannel {
            public function channelName(): string { return 'mail'; }
            public function label(): string { return 'Email'; }
            public function configFields(): array { return []; }
            public function validationRules(): array { return []; }
            public function isConfiguredFor(User $user): bool { return true; }
            public function send(User $user, Domain $domain, bool $isUp): void {}
        };
    }

    public function test_mail_notification_sent_and_marked_as_sent(): void
    {
        $this->app->bind(MailDomainChannel::class, fn () => $this->noopMailChannel());

        $record = $this->makeNotificationRecord('mail');

        (new SendDomainNotification($record))->handle();

        $this->assertDatabaseHas('domain_check_notifications', [
            'id' => $record->id,
            'status' => 'sent',
            'attempts' => 1,
        ]);
    }

    public function test_failed_notification_is_retried(): void
    {
        Queue::fake([SendDomainNotification::class]);

        $this->app->bind(MailDomainChannel::class, fn () => new class implements DomainNotificationChannel {
            public function channelName(): string { return 'mail'; }
            public function label(): string { return 'Email'; }
            public function configFields(): array { return []; }
            public function validationRules(): array { return []; }
            public function isConfiguredFor(User $user): bool { return true; }
            public function send(User $user, Domain $domain, bool $isUp): void
            {
                throw new \RuntimeException('SMTP error');
            }
        });

        $record = $this->makeNotificationRecord('mail');

        (new SendDomainNotification($record))->handle();

        $record->refresh();
        $this->assertEquals(1, $record->attempts);
        $this->assertEquals('pending', $record->status);
        $this->assertStringContainsString('SMTP error', $record->last_error);
        Queue::assertPushed(SendDomainNotification::class);
    }

    public function test_notification_marked_failed_after_max_retries(): void
    {
        $this->app->bind(MailDomainChannel::class, fn () => new class implements DomainNotificationChannel {
            public function channelName(): string { return 'mail'; }
            public function label(): string { return 'Email'; }
            public function configFields(): array { return []; }
            public function validationRules(): array { return []; }
            public function isConfiguredFor(User $user): bool { return true; }
            public function send(User $user, Domain $domain, bool $isUp): void
            {
                throw new \RuntimeException('Connection refused');
            }
        });

        $record = $this->makeNotificationRecord('mail');
        $record->update(['attempts' => 2]);

        (new SendDomainNotification($record))->handle();

        $record->refresh();
        $this->assertEquals('failed', $record->status);
        $this->assertEquals(3, $record->attempts);
    }

    public function test_unknown_channel_marked_failed(): void
    {
        $record = $this->makeNotificationRecord('unknown');

        (new SendDomainNotification($record))->handle();

        $this->assertDatabaseHas('domain_check_notifications', [
            'id' => $record->id,
            'status' => 'failed',
        ]);
    }
}
