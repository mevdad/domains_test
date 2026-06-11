<?php

namespace App\Contracts;

use App\Models\Domain;
use App\Models\User;

interface DomainNotificationChannel
{
    public function channelName(): string;

    public function label(): string;

    public function icon(): string;

    /** @return array<int, array{key: string, label: string, type: string, placeholder: string}> */
    public function configFields(): array;

    /** @return array<string, mixed> */
    public function validationRules(): array;

    public function isConfiguredFor(User $user): bool;

    public function send(User $user, Domain $domain, bool $isUp): void;
}
