<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'edit task title',
            'change due date',
            'send feedback',
            'create task',
            'delete task',
            'assign task',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
            ]);
        }

        $manager = Role::firstOrCreate([
            'name' => 'manager',
        ]);

        $employee = Role::firstOrCreate([
            'name' => 'employee',
        ]);

        $manager->givePermissionTo(Permission::all());
    }
}