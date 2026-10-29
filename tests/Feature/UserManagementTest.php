<?php

namespace Tests\Feature;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Models\Post;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_a_user_and_grant_admin_access(): void
    {
        $this->actingAs(User::factory()->admin()->create());
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        Livewire::test(CreateUser::class)->fillForm([
            'name' => 'New editor', 'email' => 'editor@example.com',
            'password' => 'a-secure-password', 'password_confirmation' => 'a-secure-password',
            'is_admin' => true,
        ])->call('create')->assertHasNoFormErrors();

        $this->assertDatabaseHas('users', ['name' => 'New editor', 'email' => 'editor@example.com', 'is_admin' => true]);
        $created = User::where('email', 'editor@example.com')->firstOrFail();
        $this->assertTrue(Hash::check('a-secure-password', $created->password));
    }

    public function test_editing_user_keeps_blank_password_and_can_revoke_another_admin(): void
    {
        $this->actingAs(User::factory()->admin()->create());
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        $target = User::factory()->admin()->create();
        $originalPassword = $target->password;

        Livewire::test(EditUser::class, ['record' => $target->id])
            ->assertFormSet(['password' => null])
            ->fillForm(['name' => 'Updated editor', 'password' => '', 'is_admin' => false])
            ->call('save')->assertHasNoFormErrors();

        $this->assertDatabaseHas('users', ['id' => $target->id, 'name' => 'Updated editor', 'is_admin' => false]);
        $this->assertSame($originalPassword, $target->fresh()->password);
    }

    public function test_admin_can_change_a_users_password(): void
    {
        $this->actingAs(User::factory()->admin()->create());
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        $target = User::factory()->create();

        Livewire::test(EditUser::class, ['record' => $target->id])->fillForm([
            'password' => 'a-new-secure-password', 'password_confirmation' => 'a-new-secure-password',
        ])->call('save')->assertHasNoFormErrors();

        $this->assertTrue(Hash::check('a-new-secure-password', $target->fresh()->password));
    }

    public function test_admin_cannot_revoke_own_access_or_delete_own_account(): void
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        Livewire::test(EditUser::class, ['record' => $admin->id])
            ->fillForm(['is_admin' => false])->call('save')
            ->assertHasFormErrors(['is_admin'])
            ->assertSee('You cannot revoke your own administrator access.');
        Livewire::test(EditUser::class, ['record' => $admin->id])->assertActionHidden('delete');

        $this->assertDatabaseHas('users', ['id' => $admin->id, 'is_admin' => true]);
    }

    public function test_admin_can_delete_unused_non_admin_accounts_but_not_authors_or_admins(): void
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        $unused = User::factory()->create();
        $otherAdmin = User::factory()->admin()->create();
        $post = Post::factory()->create();

        Livewire::test(EditUser::class, ['record' => $unused->id])->callAction('delete');
        Livewire::test(EditUser::class, ['record' => $otherAdmin->id])->assertActionHidden('delete');
        Livewire::test(EditUser::class, ['record' => $post->user_id])->assertActionHidden('delete');

        $this->assertModelMissing($unused);
        $this->assertModelExists($otherAdmin);
        $this->assertModelExists($post);
        $this->assertFalse(Gate::forUser($admin)->allows('deleteAny', User::class));
    }

    public function test_form_rejects_invalid_identity_and_password_fields(): void
    {
        $this->actingAs(User::factory()->admin()->create(['email' => 'taken@example.com']));
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        Livewire::test(CreateUser::class)->fillForm([
            'name' => '', 'email' => 'taken@example.com', 'password' => 'short',
            'password_confirmation' => 'different',
        ])->call('create')->assertHasFormErrors([
            'name' => 'required', 'email' => 'unique', 'password' => ['min', 'confirmed'],
        ]);
        Livewire::test(CreateUser::class)->fillForm(['email' => 'invalid', 'password' => null])
            ->call('create')->assertHasFormErrors(['email' => 'email', 'password' => 'required']);

        $this->assertDatabaseCount('users', 1);
    }

    public function test_non_admins_cannot_manage_users_or_grant_permissions(): void
    {
        $target = User::factory()->create();
        $reader = User::factory()->create();
        $this->get('/admin/users')->assertRedirect('/admin/login');
        $this->actingAs($reader);

        $this->get('/admin/users')->assertForbidden();
        $this->get('/admin/users/create')->assertForbidden();
        $this->get('/admin/users/'.$target->id.'/edit')->assertForbidden();
        $this->assertFalse(Gate::forUser($reader)->allows('create', User::class));
        $this->assertFalse(Gate::forUser($reader)->allows('update', $target));
        $this->assertFalse(Gate::forUser($reader)->allows('delete', $target));
    }
}
