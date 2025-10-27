<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Technical Documents',
                'description' => 'Technical specifications, manuals, and guides',
                'color' => '#007bff',
                'sort_order' => 1,
            ],
            [
                'name' => 'HR Documents',
                'description' => 'Human resources related documents and policies',
                'color' => '#28a745',
                'sort_order' => 2,
            ],
            [
                'name' => 'Financial Reports',
                'description' => 'Budget, financial statements and reports',
                'color' => '#ffc107',
                'sort_order' => 3,
            ],
            [
                'name' => 'Meeting Minutes',
                'description' => 'Records and minutes of meetings',
                'color' => '#dc3545',
                'sort_order' => 4,
            ],
            [
                'name' => 'Policies',
                'description' => 'Company policies and procedures',
                'color' => '#6f42c1',
                'sort_order' => 5,
            ],
            [
                'name' => 'Templates',
                'description' => 'Document templates and forms',
                'color' => '#20c997',
                'sort_order' => 6,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}