<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionsSeeder extends Seeder
{
    protected const PERMISSION_GROUPS = [
        'Expense' => [
            'expense-create',
            'expense-view',
            'expense-edit',
            'expense-delete',
        ], 
        'Vendor' => [
            'vendor-create',
            'vendor-view',
            'vendor-edit',
            'vendor-delete',
        ],
        'Customer' => [
            'customer-create',
            'customer-view',
            'customer-edit',
            'customer-delete',
        ],
        'Transfer' => [
            'transfer-create',
            'transfer-view',
        ],
        'Sales' => [
            'sales-create',
            'sales-view',
            'sales-edit',
            'sales-delete',
        ],
        'Purchase' => [
            'purchase-create',
            'purchase-view',
            'purchase-edit',
            'purchase-delete',
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
