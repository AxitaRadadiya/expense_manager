<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            'Income',
            'Expense',
        ];
        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['name' => $category],
                [
                    'name' => $category,
                ]
            );
        }
    }
}