<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class CreateDefaultUser extends Command
{
    protected $signature = 'user:create {--name=} {--email=} {--password=}';

    protected $description = 'Create the single panel admin account (fails if one already exists)';

    public function handle(): int
    {
        if (User::query()->exists()) {
            $this->error('A user account already exists. Only one account is allowed — delete it first if you need to replace it.');

            return self::FAILURE;
        }

        $name = $this->option('name') ?: $this->ask('Name');
        $email = $this->option('email') ?: $this->ask('Email');
        $password = $this->option('password') ?: $this->secret('Password');

        $validator = Validator::make(
            ['name' => $name, 'email' => $email, 'password' => $password],
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255'],
                'password' => ['required', 'string', 'min:8'],
            ]
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ]);

        $this->info("Created user #{$user->id} ({$user->email}).");
        $this->line('2FA setup will be required on first login to /panel.');

        return self::SUCCESS;
    }
}
