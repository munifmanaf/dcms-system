<?php
// database/seeders/DemoDataSeeder.php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Community;
use App\Models\Collection;
use App\Models\Item;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SampleDataSeeder extends Seeder
{
    public function run()
    {
        // Clear existing data
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Item::truncate();
        Collection::truncate();
        Community::truncate();
        User::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Create admin user
        $user = User::create([
            'email' => 'admin@repository.edu',
            'name' => 'Repository Admin',
            'password' => bcrypt('password'),
        ]);

        // Create researcher users
        $researcher1 = User::create([
            'email' => 'researcher1@university.edu',
            'name' => 'Dr. Sarah Chen',
            'password' => bcrypt('password'),
        ]);

        $researcher2 = User::create([
            'email' => 'researcher2@university.edu',
            'name' => 'Prof. Michael Brown',
            'password' => bcrypt('password'),
        ]);

        // Create Communities
        $engineering = Community::create(['name' => 'Faculty of Engineering', 'description' => 'Engineering research outputs']);
        $science = Community::create(['name' => 'Faculty of Science', 'description' => 'Scientific research publications']);
        $arts = Community::create(['name' => 'Faculty of Arts & Humanities', 'description' => 'Arts and humanities research']);
        $medicine = Community::create(['name' => 'Faculty of Medicine', 'description' => 'Medical research and studies']);

        // Create Collections
        $collections = [
            'engineering' => [
                'Masters Theses' => $engineering->id,
                'Research Papers' => $engineering->id,
                'Technical Reports' => $engineering->id,
            ],
            'science' => [
                'Journal Articles' => $science->id,
                'Research Datasets' => $science->id,
                'Scientific Images' => $science->id,
                'Research Videos' => $science->id,
            ],
            'arts' => [
                'Literary Works' => $arts->id,
                'Research Papers' => $arts->id,
                'Creative Works' => $arts->id,
                'Artwork Images' => $arts->id,
            ],
            'medicine' => [
                'Clinical Studies' => $medicine->id,
                'Research Papers' => $medicine->id,
                'Case Studies' => $medicine->id,
                'Medical Imaging' => $medicine->id,
            ]
        ];

        $collectionModels = [];
        foreach ($collections as $faculty => $facultyCollections) {
            foreach ($facultyCollections as $name => $communityId) {
                $collectionModels[$name] = Collection::create([
                    'name' => $name,
                    'description' => "Collection for {$name}",
                    'community_id' => $communityId
                ]);
            }
        }

        // Sample items data with ALL file types
        $itemsData = [
            // Engineering Items - PDF & Word
            [
                'title' => 'Renewable Energy Solutions for Urban Areas',
                'description' => 'Comprehensive study of renewable energy integration in modern urban environments.',
                'file_type' => 'PDF',
                'collection_id' => $collectionModels['Masters Theses']->id,
                'workflow_state' => 'published',
                'download_count' => rand(50, 200),
                'view_count' => rand(100, 500),
                'user_id' => $researcher1->id,
                'metadata' => [
                    'dc_title' => ['Renewable Energy Solutions for Urban Areas'],
                    'dc_creator' => ['John Smith', 'Dr. Sarah Chen'],
                    'dc_subject' => ['Renewable Energy', 'Urban Planning', 'Sustainability'],
                    'dc_description' => ['Comprehensive study of renewable energy integration'],
                    'dc_date_issued' => ['2024-06-15'],
                    'dc_type' => ['Thesis'],
                    'dc_publisher' => ['University Engineering Department'],
                    'dc_format' => ['PDF'],
                    'dc_identifier' => ['ETD-2024-001']
                ],
                'created_at' => now()->subMonths(2),
            ],
            [
                'title' => 'Machine Learning in Structural Engineering',
                'description' => 'AI and ML techniques for structural analysis and design optimization.',
                'file_type' => 'PDF',
                'collection_id' => $collectionModels['Research Papers']->id,
                'workflow_state' => 'published',
                'download_count' => rand(80, 300),
                'view_count' => rand(150, 600),
                'user_id' => $researcher2->id,
                'metadata' => [
                    'dc_title' => ['Machine Learning in Structural Engineering'],
                    'dc_creator' => ['Dr. Michael Brown', 'Emily Johnson'],
                    'dc_subject' => ['Machine Learning', 'Structural Engineering', 'AI'],
                    'dc_description' => ['AI and ML techniques for structural analysis'],
                    'dc_date_issued' => ['2024-05-20'],
                    'dc_type' => ['Research Paper'],
                    'dc_publisher' => ['Engineering Research Center'],
                    'dc_format' => ['PDF'],
                    'dc_identifier' => ['RP-2024-002']
                ],
                'created_at' => now()->subMonths(3),
            ],

            // Science Items - Images & Videos
            [
                'title' => 'Microscopic Analysis of Cellular Structures',
                'description' => 'High-resolution microscopic images showing detailed cellular structures and organelles in biological samples.',
                'file_type' => 'Image',
                'collection_id' => $collectionModels['Scientific Images']->id,
                'workflow_state' => 'published',
                'download_count' => rand(40, 150),
                'view_count' => rand(200, 600),
                'user_id' => $researcher1->id,
                'metadata' => [
                    'dc_title' => ['Microscopic Analysis of Cellular Structures'],
                    'dc_creator' => ['Dr. Lisa Wang', 'Biology Imaging Team'],
                    'dc_subject' => ['Microscopy', 'Cellular Biology', 'Image Analysis', 'Scientific Imaging'],
                    'dc_description' => ['High-resolution microscopic images showing detailed cellular structures'],
                    'dc_date_issued' => ['2024-03-22'],
                    'dc_type' => ['Research Images'],
                    'dc_publisher' => ['Biology Research Center'],
                    'dc_format' => ['JPG'],
                    'dc_identifier' => ['IMG-2024-003']
                ],
                'created_at' => now()->subMonths(5),
            ],
            [
                'title' => 'Laboratory Techniques: Advanced PCR Methods',
                'description' => 'Instructional video demonstrating advanced PCR techniques and best practices in molecular biology laboratory.',
                'file_type' => 'Video',
                'collection_id' => $collectionModels['Research Videos']->id,
                'workflow_state' => 'published',
                'download_count' => rand(60, 180),
                'view_count' => rand(300, 800),
                'user_id' => $researcher2->id,
                'metadata' => [
                    'dc_title' => ['Laboratory Techniques: Advanced PCR Methods'],
                    'dc_creator' => ['Dr. James Wilson', 'Molecular Biology Department'],
                    'dc_subject' => ['PCR', 'Molecular Biology', 'Laboratory Techniques', 'Video Tutorial'],
                    'dc_description' => ['Instructional video demonstrating advanced PCR techniques'],
                    'dc_date_issued' => ['2024-01-15'],
                    'dc_type' => ['Training Video'],
                    'dc_publisher' => ['Science Faculty'],
                    'dc_format' => ['MP4'],
                    'dc_identifier' => ['VID-2024-004']
                ],
                'created_at' => now()->subMonths(7),
            ],
            [
                'title' => 'Climate Change Data Analysis 2020-2024',
                'description' => 'Comprehensive dataset of climate change indicators and environmental measurements.',
                'file_type' => 'Dataset',
                'collection_id' => $collectionModels['Research Datasets']->id,
                'workflow_state' => 'published',
                'download_count' => rand(100, 400),
                'view_count' => rand(200, 800),
                'user_id' => $researcher1->id,
                'metadata' => [
                    'dc_title' => ['Climate Change Data Analysis 2020-2024'],
                    'dc_creator' => ['Climate Research Team'],
                    'dc_subject' => ['Climate Change', 'Environmental Data', 'Dataset'],
                    'dc_description' => ['Comprehensive dataset of climate change indicators'],
                    'dc_date_issued' => ['2024-02-18'],
                    'dc_type' => ['Dataset'],
                    'dc_publisher' => ['Environmental Research Institute'],
                    'dc_format' => ['CSV'],
                    'dc_identifier' => ['DATA-2024-005']
                ],
                'created_at' => now()->subMonths(6),
            ],

            // Arts & Humanities - Images & Documents
            [
                'title' => 'Modernist Poetry in Digital Literature',
                'description' => 'Analysis of modernist poetry adaptations in contemporary digital media.',
                'file_type' => 'Word Document',
                'collection_id' => $collectionModels['Literary Works']->id,
                'workflow_state' => 'published',
                'download_count' => rand(30, 120),
                'view_count' => rand(80, 200),
                'user_id' => $researcher1->id,
                'metadata' => [
                    'dc_title' => ['Modernist Poetry in Digital Literature'],
                    'dc_creator' => ['Dr. Elizabeth Wong'],
                    'dc_subject' => ['Modernist Poetry', 'Digital Humanities', 'Literary Analysis'],
                    'dc_description' => ['Analysis of modernist poetry adaptations'],
                    'dc_date_issued' => ['2024-08-05'],
                    'dc_type' => ['Literary Analysis'],
                    'dc_publisher' => ['Literature Department'],
                    'dc_format' => ['DOCX'],
                    'dc_identifier' => ['LIT-2024-006']
                ],
                'created_at' => now()->subMonths(1),
            ],
            [
                'title' => 'Contemporary Art Exhibition Catalog 2024',
                'description' => 'Digital catalog featuring high-quality images from the annual contemporary art exhibition.',
                'file_type' => 'Image',
                'collection_id' => $collectionModels['Artwork Images']->id,
                'workflow_state' => 'published',
                'download_count' => rand(25, 100),
                'view_count' => rand(150, 400),
                'user_id' => $researcher2->id,
                'metadata' => [
                    'dc_title' => ['Contemporary Art Exhibition Catalog 2024'],
                    'dc_creator' => ['Arts Department', 'Visual Arts Faculty'],
                    'dc_subject' => ['Contemporary Art', 'Exhibition', 'Art Catalog', 'Visual Arts'],
                    'dc_description' => ['Digital catalog featuring high-quality images from art exhibition'],
                    'dc_date_issued' => ['2024-07-20'],
                    'dc_type' => ['Art Catalog'],
                    'dc_publisher' => ['University Art Gallery'],
                    'dc_format' => ['JPG'],
                    'dc_identifier' => ['ART-2024-007']
                ],
                'created_at' => now()->subMonths(2),
            ],

            // Medicine - Images & Datasets
            [
                'title' => 'Clinical Trial: Cardiovascular Treatments',
                'description' => 'Dataset from clinical trials investigating cardiovascular treatments.',
                'file_type' => 'Dataset',
                'collection_id' => $collectionModels['Clinical Studies']->id,
                'workflow_state' => 'published',
                'download_count' => rand(70, 280),
                'view_count' => rand(140, 350),
                'user_id' => $researcher1->id,
                'metadata' => [
                    'dc_title' => ['Clinical Trial: Cardiovascular Treatments'],
                    'dc_creator' => ['Cardiology Research Team'],
                    'dc_subject' => ['Clinical Trials', 'Cardiovascular', 'Medical Data'],
                    'dc_description' => ['Dataset from clinical trials'],
                    'dc_date_issued' => ['2024-09-12'],
                    'dc_type' => ['Clinical Data'],
                    'dc_publisher' => ['Medical Research Center'],
                    'dc_format' => ['XLSX'],
                    'dc_identifier' => ['CLIN-2024-008']
                ],
                'created_at' => now()->subDays(30),
            ],
            [
                'title' => 'MRI Brain Scan Analysis',
                'description' => 'Collection of MRI brain scans with detailed annotations for neurological research.',
                'file_type' => 'Image',
                'collection_id' => $collectionModels['Medical Imaging']->id,
                'workflow_state' => 'published',
                'download_count' => rand(45, 160),
                'view_count' => rand(180, 500),
                'user_id' => $researcher2->id,
                'metadata' => [
                    'dc_title' => ['MRI Brain Scan Analysis'],
                    'dc_creator' => ['Neurology Department', 'Medical Imaging Team'],
                    'dc_subject' => ['MRI', 'Brain Scan', 'Neurology', 'Medical Imaging'],
                    'dc_description' => ['Collection of MRI brain scans with detailed annotations'],
                    'dc_date_issued' => ['2024-04-10'],
                    'dc_type' => ['Medical Images'],
                    'dc_publisher' => ['Medical School'],
                    'dc_format' => ['DICOM'],
                    'dc_identifier' => ['MED-2024-009']
                ],
                'created_at' => now()->subMonths(4),
            ],
            [
                'title' => 'Surgical Procedure Demonstration Video',
                'description' => 'Step-by-step video demonstration of advanced laparoscopic surgical techniques.',
                'file_type' => 'Video',
                'collection_id' => $collectionModels['Clinical Studies']->id,
                'workflow_state' => 'published',
                'download_count' => rand(55, 220),
                'view_count' => rand(250, 700),
                'user_id' => $researcher1->id,
                'metadata' => [
                    'dc_title' => ['Surgical Procedure Demonstration Video'],
                    'dc_creator' => ['Dr. Robert Kim', 'Surgical Training Department'],
                    'dc_subject' => ['Surgery', 'Medical Training', 'Laparoscopic', 'Video Demonstration'],
                    'dc_description' => ['Step-by-step video demonstration of surgical techniques'],
                    'dc_date_issued' => ['2024-05-05'],
                    'dc_type' => ['Training Video'],
                    'dc_publisher' => ['Medical Training Center'],
                    'dc_format' => ['MP4'],
                    'dc_identifier' => ['SURG-2024-010']
                ],
                'created_at' => now()->subMonths(3),
            ],

            // Draft & Pending Items
            [
                'title' => 'Sustainable Urban Development Framework - Draft',
                'description' => 'Proposed framework for sustainable urban development initiatives.',
                'file_type' => 'Word Document',
                'collection_id' => $collectionModels['Research Papers']->id,
                'workflow_state' => 'draft',
                'download_count' => 0,
                'view_count' => rand(1, 5),
                'user_id' => $user->id,
                'metadata' => [
                    'dc_title' => ['Sustainable Urban Development Framework'],
                    'dc_creator' => ['Urban Planning Research Group'],
                    'dc_subject' => ['Urban Development', 'Sustainability', 'Framework'],
                    'dc_description' => ['Proposed framework for sustainable urban development'],
                    'dc_date_issued' => ['2024-10-10'],
                    'dc_type' => ['Research Paper'],
                    'dc_publisher' => ['Urban Planning Department'],
                    'dc_format' => ['DOCX'],
                    'dc_identifier' => ['RP-2024-011']
                ],
                'created_at' => now()->subDays(3),
            ],
            [
                'title' => 'Digital Art Installation Documentation - Under Review',
                'description' => 'Documentation and images of interactive digital art installation.',
                'file_type' => 'Image',
                'collection_id' => $collectionModels['Artwork Images']->id,
                'workflow_state' => 'pending_review',
                'download_count' => rand(2, 10),
                'view_count' => rand(10, 30),
                'user_id' => $researcher2->id,
                'metadata' => [
                    'dc_title' => ['Digital Art Installation Documentation'],
                    'dc_creator' => ['Digital Arts Collective'],
                    'dc_subject' => ['Digital Art', 'Interactive Installation', 'Contemporary Art'],
                    'dc_description' => ['Documentation and images of interactive digital art installation'],
                    'dc_date_issued' => ['2024-10-08'],
                    'dc_type' => ['Art Documentation'],
                    'dc_publisher' => ['Arts Faculty'],
                    'dc_format' => ['JPG'],
                    'dc_identifier' => ['ART-2024-012']
                ],
                'created_at' => now()->subDays(5),
            ],
        ];

        foreach ($itemsData as $itemData) {
            Item::create(array_merge($itemData, [
                'last_downloaded_at' => $itemData['download_count'] > 0 ? now()->subDays(rand(1, 30)) : null,
                'last_viewed_at' => now()->subDays(rand(1, 7)),
                'updated_at' => $itemData['created_at'],
            ]));
        }

        $this->command->info('🎉 Demo data seeded successfully!');
        $this->command->info('👥 Users: ' . User::count());
        $this->command->info('🏛️ Communities: ' . Community::count());
        $this->command->info('📁 Collections: ' . Collection::count());
        $this->command->info('📄 Items: ' . Item::count());
        $this->command->info('📊 File Types:');
        $this->command->info('   📄 PDF: ' . Item::where('file_type', 'PDF')->count());
        $this->command->info('   📝 Word: ' . Item::where('file_type', 'Word Document')->count());
        $this->command->info('   🖼️ Images: ' . Item::where('file_type', 'Image')->count());
        $this->command->info('   🎥 Videos: ' . Item::where('file_type', 'Video')->count());
        $this->command->info('   📊 Datasets: ' . Item::where('file_type', 'Dataset')->count());
        $this->command->info('⬇️ Total Downloads: ' . Item::sum('download_count'));
        $this->command->info('👀 Total Views: ' . Item::sum('view_count'));
    }
}