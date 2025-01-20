<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use App\Enums\User\Active;
use App\Enums\User\Roles;
use App\Enums\User\Statuses;
use Illuminate\Support\Facades\Artisan;

class AppInitialize extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:initialize';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize the application by running migrations, seeding, and other setup tasks.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Initializing the application...');
        $this->newLine();

        $this->info('Running migrations...');
        Artisan::call('migrate', ['--force' => true]);
        $this->newLine();

        $this->info('Seeding the database...');
        Artisan::call('db:seed', ['--force' => true]);
        $this->newLine();

        $this->info('Clearing cache...');
        Artisan::call('optimize');
        $this->newLine();

        $this->info('Creating admin user...');

        $credentials = [
            'name' => "Superadmin",
            'email' => "admin@gmail.com",
            'email_verified' => Carbon::now(),
            'password' => 'password',
            'active' => Active::YES,
            'role' => Roles::ADMIN,
            'status' => Statuses::APPROVED
        ];
        User::create($credentials + [
            'password' => Hash::make("password")
        ]);

        $this->newLine();
        $this->info("Email: " . $credentials['email']);
        $this->info("Password: " . $credentials['password']);
        $this->newLine();

        $this->info('Application initialized successfully!');
    }
}
