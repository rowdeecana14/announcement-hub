<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Enums\User\Active;
use App\Enums\User\Roles;
use App\Enums\User\Statuses;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-admin-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->ask('Full name');
        $emailMatch = false;

        while (!$emailMatch) {
            $email = $this->ask('Email address');
            $userExists = User::where('email', $email)->exists();
            $emailMatch = ($emailMatch == $userExists);

            if ($userExists) {
                $this->error('Email is already in used. Try again.');
                $this->newLine();
            }
        }

        $passwordMatch = false;

        while (!$passwordMatch) {
            $password = $this->secret('Provide a password');
            $confirmation = $this->secret('Enter the password again');

            $passwordMatch = ($password == $confirmation);

            if (!$passwordMatch) {
                $this->error('Passwords do not match. Try again.');
                $this->newLine();
            }
        }

        User::create([
            'name' => $name,
            'email' => $email,
            'email_verified' => Carbon::now(),
            'password' => Hash::make($password),
            'active' => Active::YES,
            'role' => Roles::ADMIN,
            'status' => Statuses::APPROVED
        ]);

        $this->info('Admin user created successfully!');
    }
}
