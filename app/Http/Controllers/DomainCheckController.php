<?php

namespace App\Http\Controllers;

use App\Contracts\DomainNotificationChannel;
use App\Models\Domain;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DomainCheckController extends Controller
{
    public function index(Request $request, Domain $domain): Response
    {
        abort_if($domain->user_id !== $request->user()->id, 403);

        return Inertia::render('domains/checks', [
            'domain' => $domain,
            'checks' => $domain->checks()->with('notifications')->paginate(20),
            'enabledChannels' => $this->enabledChannels($request->user()),
        ]);
    }

    /** @return array<int, array{name: string, label: string, icon: string}> */
    private function enabledChannels(User $user): array
    {
        return collect(app()->tagged('domain-notification-channels'))
            ->filter(fn (DomainNotificationChannel $ch) => $user->isChannelEnabled($ch->channelName()))
            ->map(fn (DomainNotificationChannel $ch) => [
                'name' => $ch->channelName(),
                'label' => $ch->label(),
                'icon' => $ch->icon(),
            ])
            ->values()
            ->all();
    }
}
