<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    /** @param array<string, mixed> $data */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return DB::transaction(function () use ($record, $data): User {
            User::query()->where('is_admin', true)->orderBy('id')->lockForUpdate()->get();
            $actor = auth()->user()->fresh();
            $user = User::query()->lockForUpdate()->findOrFail($record->getKey());
            Gate::forUser($actor)->authorize('update', $user);
            $isAdmin = (bool) ($data['is_admin'] ?? $user->is_admin);

            if ($user->is($actor) && ! $isAdmin) {
                throw ValidationException::withMessages([
                    'data.is_admin' => 'You cannot revoke your own administrator access.',
                ]);
            }

            $user->fill(collect($data)->only(['name', 'email', 'password'])->all());
            $user->is_admin = $isAdmin;
            $user->save();

            return $user;
        });
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->modalDescription('Only non-admin accounts without authored posts can be deleted. This permanently removes the account.'),
        ];
    }
}
