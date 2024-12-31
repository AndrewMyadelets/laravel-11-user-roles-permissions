<?php

namespace Database\Seeders;

use App\Enums\RoleName;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Collection;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->createAdminRole();
        $this->createStaffRole();
        $this->createCustomerRole();

        $this->command->info('Roles table seeded.');
    }

    /**
     * Create a new role.
     *
     * @param RoleName $role
     * @param Collection $permissions
     * @return void
     */
    protected function createRole(RoleName $role, Collection $permissions): void
    {
        $newRole = Role::create(['name' => $role->value]);
        $newRole->permissions()->sync($permissions);
    }

    /**
     * Create an admin role.
     *
     * @return void
     */
    protected function createAdminRole(): void
    {
        $permissions = Permission::all()->pluck('id');

        $this->createRole(RoleName::ADMIN, $permissions);
    }

    /**
     * Create a staff role.
     *
     * @return void
     */
    protected function createStaffRole(): void
    {
        $permissions = Permission::query()
            ->where('name', 'admin.panel.access')
            ->orWhere('name', 'users')
            ->orWhere('name', 'like', 'products.%')
            ->pluck('id');

        $this->createRole(RoleName::STAFF, $permissions);
    }

    /**
     * Create a customer role.
     *
     * @return void
     */
    protected function createCustomerRole(): void
    {
        $permissions = Permission::query()
            ->where('name', 'admin.panel.access')
            ->orWhere('name', 'products')
            ->orWhere('name', 'products.view')
            ->get();

        $this->createRole(RoleName::CUSTOMER, $permissions);
    }
}
