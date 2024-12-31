<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::create(['name' => 'admin.panel.access', 'display_name' => 'Access admin panel']);

        $resources = [
            'users',
            'roles',
            'products'
        ];

        array_map(
            fn($resource) => Permission::create([
                'name' => $resource,
                'display_name' => $this->getDisplayName($resource),
                'parent_id' => Permission::query()->where('name', 'admin.panel.access')->first()->id,
            ]),
            $resources
        );

        $actions = [
            'view',
            'create',
            'update',
            'delete',
            'massDelete',
            'forceDelete',
            'restore',
        ];

        collect($resources)
            ->crossJoin($actions)
            ->map(function ($set) {
                return [
                    'name' => implode('.', $set),
                    'display_name' => $this->getDisplayName(array_reverse($set)),
                    'parent_id' => $this->getParentId($set[0]),
                ];
            })->each(function ($permission) {
                Permission::create([
                    'name' => $permission['name'],
                    'display_name' => $permission['display_name'],
                    'parent_id' => $permission['parent_id']
                ]);
            });

        $this->command->info('Permissions table seeded.');
    }

    /**
     * Get permission display name.
     *
     * @param string|array $input
     * @return string
     */
    private function getDisplayName(string|array $input): string
    {
        if (is_array($input)) {
            $processedItems = array_map(
                fn($item) => strtolower(preg_replace('/([a-z])([A-Z])/', '$1 $2', $item)),
                $input
            );
            $processed = implode(' ', $processedItems);
        } else {
            $processed = strtolower(preg_replace('/([a-z])([A-Z])/', '$1 $2', $input));
        }

        return ucfirst($processed);
    }

    /**
     * Get permission parent id.
     *
     * @param string $input
     * @return int|null
     */
    private function getParentId(string $input): ?int
    {
        return Permission::query()->where('name', $input)->first()->id ?? null;
    }
}
