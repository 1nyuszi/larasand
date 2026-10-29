<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_is_denied_even_with_a_gmail_address(): void
    {
        $user = User::factory()->create(['email' => 'reader@gmail.com']);

        $this->actingAs($user)->get('/admin')->assertForbidden();
    }

    public function test_admin_can_access_regardless_of_email_domain(): void
    {
        $user = User::factory()->admin()->create(['email' => 'editor@example.com']);

        $this->actingAs($user)->get('/admin')->assertOk();
    }

    public function test_admin_flag_cannot_be_mass_assigned(): void
    {
        $user = User::factory()->create();
        $user->fill(['name' => 'Updated name', 'is_admin' => true])->save();

        $this->assertDatabaseHas('users', ['id' => $user->id, 'is_admin' => false]);
        $this->actingAs($user->fresh())->get('/admin')->assertForbidden();
    }

    public function test_command_can_grant_and_revoke_admin_access(): void
    {
        $user = User::factory()->create(['email' => 'editor@example.com']);

        $this->artisan('users:set-admin', ['email' => $user->email])->assertSuccessful();
        $this->assertDatabaseHas('users', ['id' => $user->id, 'is_admin' => true]);
        $this->actingAs($user->fresh())->get('/admin')->assertOk();

        $this->artisan('users:set-admin', ['email' => $user->email, '--revoke' => true])->assertSuccessful();
        $this->assertDatabaseHas('users', ['id' => $user->id, 'is_admin' => false]);
        $this->actingAs($user->fresh())->get('/admin')->assertForbidden();
    }

    public function test_command_rejects_an_unknown_account(): void
    {
        $this->artisan('users:set-admin', ['email' => 'missing@example.com'])
            ->expectsOutput('User not found.')
            ->assertFailed();
        $this->assertDatabaseCount('users', 0);
    }
}
