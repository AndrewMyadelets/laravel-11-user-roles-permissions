<?php

namespace Database\Seeders;

use App\Enums\RoleName;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->createAdminUser();
        $this->createStaffUser();
        $this->createCustomerUser();

        $roles = Role::all();

        if ($roles->isEmpty()) {
            $this->command->warn('No roles found in the database. Please seed roles first.');
        }

        User::factory(100)
            ->create()
            ->each(function (User $user) use ($roles) {
                $user->roles()->sync($roles->random(rand(1, 3))->pluck('id'));
            });

        $this->command->info('Users table seeded.');
    }

    /**
     * Create an admin user.
     *
     * @return void
     */
    public function createAdminUser(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('1'),
        ])->roles()->sync(Role::where('name', RoleName::ADMIN->value)->first());
    }

    /**
     * Create a staff user.
     *
     * @return void
     */
    public function createStaffUser(): void
    {
        User::create([
            'name' => 'Stuff',
            'email' => 'staff@gmail.com',
            'password' => bcrypt('1'),
        ])->roles()->sync(Role::where('name', RoleName::STAFF->value)->first());
    }

    /**
     * Create a customer user.
     *
     * @return void
     */
    public function createCustomerUser(): void
    {
        User::create([
            'name' => 'Customer',
            'email' => 'customer@gmail.com',
            'password' => bcrypt('1'),
        ])->roles()->sync(Role::where('name', RoleName::CUSTOMER->value)->first());
    }
}
