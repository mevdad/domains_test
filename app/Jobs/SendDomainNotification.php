<?php

namespace App\Jobs;

use App\Contracts\DomainNotificationChannel;
use App\Models\DomainCheckNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

class SendDomainNotification implements ShouldQueue
{
    use Queueable;

    public int $tries = 1;

    public function __construct(public DomainCheckNotification $record) {}

    public function handle(): void
    {
        $check = $this->record->check()->with('domain.user')->firstOrFail();

        /** @var iterable<DomainNotificationChannel> $channels */
        $channels = collect(app()->tagged('domain-notification-channels'));
        $channel = $channels->first(fn (DomainNotificationChannel $c) => $c->channelName() === $this->record->channel);

        if (!$channel) {
            $this->record->update(['status' => 'failed', 'last_error' => 'Unknown channel: ' . $this->record->channel]);

            return;
        }

        try {
            $channel->send($check->domain->user, $check->domain, $check->is_up);

            $this->record->update([
                'status' => 'sent',
                'sent_at' => now(),
                'attempts' => $this->record->attempts + 1,
            ]);
        } catch (Throwable $e) {
            $attempts = $this->record->attempts + 1;
            $this->record->update(['attempts' => $attempts, 'last_error' => $e->getMessage()]);

            if ($attempts < 3) {
                self::dispatch($this->record)->delay(now()->addMinutes(5 * $attempts));
            } else {
                $this->record->update(['status' => 'failed']);
            }
        }
    }
}
