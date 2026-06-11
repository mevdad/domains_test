<?php

namespace App\Http\Controllers;

use App\Contracts\DomainNotificationChannel;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CheckLogController extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('logs/index', [
            'checks' => $request->user()
                ->checks()
                ->with(['domain:id,name', 'notifications'])
                ->latest('checked_at')
                ->paginate(25),
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
