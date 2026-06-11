<?php

namespace App\Notifications;

use App\Models\Domain;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DomainAlertNotification extends Notification
{
    use Queueable;

    public function __construct(public Domain $domain, public bool $isUp) {}

    /** @return list<string> */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $status = $this->isUp ? 'UP ✅' : 'DOWN ❌';

        return (new MailMessage)
            ->subject("Domain {$this->domain->name} is {$status}")
            ->line("Your domain **{$this->domain->name}** is now **{$status}**.")
            ->action('View History', route('domains.checks.index', $this->domain));
    }
}
