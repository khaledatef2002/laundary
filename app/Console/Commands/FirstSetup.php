<?php

namespace App\Console\Commands;

use App\Enum\PermissionsType;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class FirstSetup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to setup the application for the first time';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting setup process');
        $this->call('migrate');
        $this->call('storage:link');
        $this->call('key:generate');
        $this->call('config:cache');

        $this->info('Setup process completed');

        $role = Role::create(['name' => 'Manager']);

        foreach (PermissionsType::cases() as $permission) {
            $perm = Permission::updateOrCreate(
                ['name' => $permission->value],
                []
            );

            $role->givePermissionTo($perm);
        }

        $this->info('Creating admin account');

        $randomPassword = Str::random(8);
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@' . config('app.url'),
            'password' => bcrypt($randomPassword),
        ]);

        $admin->assignRole($role);
        
        $this->info('Admin account created with email: admin@' . config('app.url') . ' and password: ' . $randomPassword);
    }
}
