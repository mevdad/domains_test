<?php

namespace App\Http\Controllers;

use App\Http\Requests\DomainStoreRequest;
use App\Http\Requests\DomainUpdateRequest;
use App\Models\Domain;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DomainController extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('domains/index', [
            'domains' => $request->user()->domains()->with('latestCheck')->orderBy('name')->get(),
        ]);
    }

    public function store(DomainStoreRequest $request): RedirectResponse
    {
        $request->user()->domains()->create($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Domain added.')]);

        return to_route('domains.index');
    }

    public function update(DomainUpdateRequest $request, Domain $domain): RedirectResponse
    {
        abort_if($domain->user_id !== $request->user()->id, 403);

        $domain->update($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Domain updated.')]);

        return to_route('domains.index');
    }

    public function destroy(Request $request, Domain $domain): RedirectResponse
    {
        abort_if($domain->user_id !== $request->user()->id, 403);

        $domain->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Domain deleted.')]);

        return to_route('domains.index');
    }
}
