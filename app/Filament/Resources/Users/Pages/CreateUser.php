<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    /** @param array<string, mixed> $data */
    protected function handleRecordCreation(array $data): Model
    {
        Gate::authorize('create', User::class);

        $user = new User;
        $user->fill(collect($data)->only(['name', 'email', 'password'])->all());
        $user->is_admin = (bool) ($data['is_admin'] ?? false);
        $user->save();

        return $user;
    }
}
