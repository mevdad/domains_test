<?php

namespace App\Notifications\Channels;

use App\Contracts\DomainNotificationChannel;
use App\Models\Domain;
use App\Models\User;
use App\Notifications\DomainAlertNotification;

class MailDomainChannel implements DomainNotificationChannel
{
    public function channelName(): string
    {
        return 'mail';
    }

    public function label(): string
    {
        return 'Email';
    }

    public function icon(): string
    {
        return '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>';
    }

    public function configFields(): array
    {
        return [];
    }

    public function validationRules(): array
    {
        return [];
    }

    public function isConfiguredFor(User $user): bool
    {
        return true;
    }

    public function send(User $user, Domain $domain, bool $isUp): void
    {
        $user->notify(new DomainAlertNotification($domain, $isUp));
    }
}
