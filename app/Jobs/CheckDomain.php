<?php

namespace App\Jobs;

use App\Models\Domain;
use App\Models\DomainCheck;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Throwable;

class CheckDomain implements ShouldQueue
{
    use Queueable;

    public function __construct(public Domain $domain) {}

    public function handle(): void
    {
        $start = microtime(true);
        $method = strtoupper($this->domain->check_method);
        $isUp = false;
        $statusCode = null;
        $responseTimeMs = null;
        $responseBody = null;
        $error = null;

        try {
            $response = Http::timeout($this->domain->check_timeout)
                ->{strtolower($method)}("https://{$this->domain->name}");
            $isUp = $response->status() < 500;
            $statusCode = $response->status();
            $responseTimeMs = (int) round((microtime(true) - $start) * 1000);
            $responseBody = $response->body();
        } catch (ConnectionException $e) {
            $error = $e->getMessage();
        } catch (Throwable $e) {
            $error = $e->getMessage();
        }

        /** @var DomainCheck $check */
        $check = $this->domain->checks()->create([
            'is_up' => $isUp,
            'method' => $method,
            'status_code' => $statusCode,
            'response_time_ms' => $responseTimeMs,
            'response_body' => $responseBody,
            'error' => $error,
            'checked_at' => now(),
        ]);

        $user = $this->domain->user;

        foreach (app()->tagged('domain-notification-channels') as $channel) {
            if ($user->isChannelEnabled($channel->channelName()) && $channel->isConfiguredFor($user)) {
                $record = $check->notifications()->create(['channel' => $channel->channelName(), 'status' => 'pending']);
                SendDomainNotification::dispatch($record);
            }
        }
    }
}
