<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('users:set-admin {email : The email of an existing user} {--revoke : Remove administrator access}')]
#[Description('Grant or revoke administrator access for an existing user')]
class SetAdmin extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $user = User::query()->where('email', $this->argument('email'))->first();

        if ($user === null) {
            $this->error('User not found.');

            return self::FAILURE;
        }

        $user->is_admin = ! $this->option('revoke');
        $user->save();

        $this->info($user->is_admin ? 'Administrator access granted.' : 'Administrator access revoked.');

        return self::SUCCESS;
    }
}
