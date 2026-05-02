<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionsSeeder extends Seeder
{
    protected const PERMISSION_GROUPS = [
        'Project' => [
            'project-create',
            'project-view',
            'project-edit',
            'project-delete',
        ],
        'Expense' => [
            'expense-create',
            'expense-view',
            'expense-edit',
            'expense-delete',
        ], 
        'Credit' => [
            'credit-create',
            'credit-view',
            'credit-edit',
            'credit-delete',
        ],
        'User' => [
            'user-create',
            'user-view',
            'user-edit',
            'user-delete',
        ],
        'Vendor' => [
            'vendor-create',
            'vendor-view',
            'vendor-edit',
            'vendor-delete',
        ],
        'Item' => [
            'item-create',
            'item-view',
            'item-edit',
            'item-delete',
        ],
        'Item Expense' => [
            'item expense-create',
            'item expense-view',
            'item expense-edit',
            'item expense-delete',
        ],
        'Transfer' => [
            'transfer-create',
            'transfer-view',
            'transfer-edit',
            'transfer-delete',
        ],
        
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissionNames = $this->permissionNames();

        Permission::whereNotIn('name', $permissionNames)->delete();

        foreach ($permissionNames as $permissionName) {
            Permission::updateOrCreate(
                ['name' => $permissionName],
                ['name' => $permissionName]
            );
        }
    }

    protected function permissionNames(): array
    {
        return collect(self::PERMISSION_GROUPS)
            ->flatten()
            ->values()
            ->all();
    }
}
