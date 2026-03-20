<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Only create permissions for the four resources the user requested
        $resources = ['project', 'expense', 'user', 'role'];
        $actions = ['create', 'view', 'edit', 'delete'];

        $records = [];
        foreach ($resources as $r) {
            foreach ($actions as $a) {
                $records[] = "{$r}-{$a}";
            }
        }

        // Create or update requested permissions (by name)
        foreach ($records as $name) {
            Permission::updateOrCreate(
                ['name' => $name],
                ['name' => $name]
            );
        }

        // Remove any permissions not in the allowed list
        Permission::whereNotIn('name', $records)->delete();
    }
}
