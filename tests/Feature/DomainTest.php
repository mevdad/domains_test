<?php

namespace Tests\Feature;

use App\Models\Domain;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DomainTest extends TestCase
{
    use RefreshDatabase;

    /** @return array<string, mixed> */
    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'example.com',
            'check_interval' => 5,
            'check_timeout' => 10,
            'check_method' => 'GET',
        ], $overrides);
    }

    public function test_guest_is_redirected_from_domains_index(): void
    {
        $this->get(route('domains.index'))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_their_domains(): void
    {
        $user = User::factory()->create();
        $domain = Domain::factory()->create(['user_id' => $user->id]);
        Domain::factory()->create(); // another user's domain — must not appear

        $this->actingAs($user)
            ->get(route('domains.index'))
            ->assertOk()
            ->assertInertia(
                fn ($page) => $page
                    ->component('domains/index')
                    ->has('domains', 1)
                    ->where('domains.0.id', $domain->id)
            );
    }

    public function test_user_can_create_a_domain(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('domains.store'), $this->validPayload())
            ->assertRedirect(route('domains.index'));

        $this->assertDatabaseHas('domains', [
            'user_id' => $user->id,
            'name' => 'example.com',
            'check_interval' => 5,
            'check_timeout' => 10,
            'check_method' => 'GET',
        ]);
    }

    public function test_user_cannot_create_duplicate_domain_name(): void
    {
        $user = User::factory()->create();
        Domain::factory()->create(['user_id' => $user->id, 'name' => 'example.com']);

        $this->actingAs($user)
            ->post(route('domains.store'), $this->validPayload())
            ->assertSessionHasErrors('name');
    }

    public function test_user_can_update_their_domain(): void
    {
        $user = User::factory()->create();
        $domain = Domain::factory()->create(['user_id' => $user->id, 'name' => 'old.com']);

        $this->actingAs($user)
            ->patch(route('domains.update', $domain), $this->validPayload(['name' => 'new.com', 'check_method' => 'HEAD']))
            ->assertRedirect(route('domains.index'));

        $this->assertDatabaseHas('domains', [
            'id' => $domain->id,
            'name' => 'new.com',
            'check_method' => 'HEAD',
        ]);
    }

    public function test_user_cannot_update_another_users_domain(): void
    {
        $user = User::factory()->create();
        $domain = Domain::factory()->create();

        $this->actingAs($user)
            ->patch(route('domains.update', $domain), $this->validPayload(['name' => 'hijacked.com']))
            ->assertForbidden();
    }

    public function test_user_can_delete_their_domain(): void
    {
        $user = User::factory()->create();
        $domain = Domain::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->delete(route('domains.destroy', $domain))
            ->assertRedirect(route('domains.index'));

        $this->assertDatabaseMissing('domains', ['id' => $domain->id]);
    }

    public function test_user_cannot_delete_another_users_domain(): void
    {
        $user = User::factory()->create();
        $domain = Domain::factory()->create();

        $this->actingAs($user)
            ->delete(route('domains.destroy', $domain))
            ->assertForbidden();
    }

    public function test_store_rejects_invalid_check_interval(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('domains.store'), $this->validPayload(['check_interval' => 7]))
            ->assertSessionHasErrors('check_interval');
    }

    public function test_store_rejects_invalid_check_method(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('domains.store'), $this->validPayload(['check_method' => 'POST']))
            ->assertSessionHasErrors('check_method');
    }

    public function test_store_rejects_check_timeout_out_of_range(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('domains.store'), $this->validPayload(['check_timeout' => 0]))
            ->assertSessionHasErrors('check_timeout');

        $this->actingAs($user)
            ->post(route('domains.store'), $this->validPayload(['check_timeout' => 61]))
            ->assertSessionHasErrors('check_timeout');
    }
}
