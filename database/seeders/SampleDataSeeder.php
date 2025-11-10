<?php
// database/seeders/DemoDataSeeder.php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Community;
use App\Models\Collection;
use App\Models\Item;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SampleDataSeeder extends Seeder
{
    public function run()
    {
        // Clear existing data
        DB::table('items')->delete();
        DB::table('collections')->delete();
        DB::table('communities')->delete();

        // Create a test user if none exists
        $user = User::first();
        // if (!$user) {
        //     $user = User::create([
        //         'name' => 'Admin User',
        //         'email' => 'admin@repository.edu',
        //         'password' => bcrypt('password'),
        //     ]);
        // }

        // Create Communities
        $engineering = Community::create([
            'name' => 'Faculty of Engineering',
            'description' => 'Research outputs from Engineering Faculty'
        ]);

        $science = Community::create([
            'name' => 'Faculty of Science', 
            'description' => 'Scientific research and publications'
        ]);

        $arts = Community::create([
            'name' => 'Faculty of Arts & Humanities',
            'description' => 'Arts, literature and humanities research'
        ]);

        $medicine = Community::create([
            'name' => 'Faculty of Medicine',
            'description' => 'Medical research and health sciences'
        ]);

        // Create Collections
        $mastersTheses = Collection::create([
            'name' => 'Masters Theses',
            'description' => 'Masters degree theses and dissertations',
            'community_id' => $engineering->id
        ]);

        $researchPapers = Collection::create([
            'name' => 'Research Papers',
            'description' => 'Published research papers and articles',
            'community_id' => $engineering->id
        ]);

        $journalArticles = Collection::create([
            'name' => 'Journal Articles',
            'description' => 'Peer-reviewed journal articles',
            'community_id' => $science->id
        ]);

        $literaryWorks = Collection::create([
            'name' => 'Literary Works',
            'description' => 'Creative writing and literary analysis',
            'community_id' => $arts->id
        ]);

        $clinicalStudies = Collection::create([
            'name' => 'Clinical Studies',
            'description' => 'Medical clinical research and case studies',
            'community_id' => $medicine->id
        ]);

        $datasets = Collection::create([
            'name' => 'Research Datasets',
            'description' => 'Research data and datasets',
            'community_id' => $science->id
        ]);

        // Create Sample Items with different file types and dates
        $items = [
            // PDF Files
            [
                'title' => 'Renewable Energy Solutions for Urban Areas',
                'description' => 'A comprehensive study of renewable energy integration in modern urban environments focusing on solar and wind power applications.',
                'file_type' => 'PDF',
                'user_id' => $user->id,
                'collection_id' => $mastersTheses->id,
                'metadata' => json_encode([
                    'dc_title' => ['Renewable Energy Solutions for Urban Areas'],
                    'dc_creator' => ['John Smith', 'Dr. Sarah Chen'],
                    'dc_subject' => ['Renewable Energy', 'Urban Planning', 'Sustainability'],
                    'dc_description' => ['A comprehensive study of renewable energy integration in modern urban environments.'],
                    'dc_date_issued' => ['2024-06-15'],
                    'dc_type' => ['Thesis'],
                    'dc_publisher' => ['University Repository'],
                    'dc_format' => ['PDF'],
                    'dc_identifier' => ['ETD-2024-001']
                ]),
                'workflow_state' => 'published',
                'created_at' => now()->subMonths(2),
                'updated_at' => now()->subMonths(2)
            ],
            [
                'title' => 'Machine Learning Applications in Structural Engineering',
                'description' => 'Exploring AI and ML techniques for structural analysis and design optimization in civil engineering projects.',
                'file_type' => 'PDF',
                'user_id' => $user->id,
                'collection_id' => $researchPapers->id,
                'metadata' => json_encode([
                    'dc_title' => ['Machine Learning Applications in Structural Engineering'],
                    'dc_creator' => ['Dr. Michael Brown', 'Emily Johnson'],
                    'dc_subject' => ['Machine Learning', 'Structural Engineering', 'Artificial Intelligence'],
                    'dc_description' => ['Exploring AI and ML techniques for structural analysis and design optimization.'],
                    'dc_date_issued' => ['2024-05-20'],
                    'dc_type' => ['Research Paper'],
                    'dc_publisher' => ['Engineering Research Center'],
                    'dc_format' => ['PDF'],
                    'dc_identifier' => ['RP-2024-002']
                ]),
                'workflow_state' => 'published',
                'created_at' => now()->subMonths(3),
                'updated_at' => now()->subMonths(3)
            ],

            // Word Documents
            [
                'title' => 'Research Proposal: Sustainable Urban Development',
                'description' => 'Detailed research proposal for sustainable urban development initiatives and implementation strategies.',
                'file_type' => 'Word Document',
                'user_id' => $user->id,
                'collection_id' => $researchPapers->id,
                'metadata' => json_encode([
                    'dc_title' => ['Research Proposal: Sustainable Urban Development'],
                    'dc_creator' => ['Dr. Robert Kim'],
                    'dc_subject' => ['Urban Development', 'Sustainability', 'Research Proposal'],
                    'dc_description' => ['Detailed research proposal for sustainable urban development initiatives.'],
                    'dc_date_issued' => ['2024-04-10'],
                    'dc_type' => ['Research Proposal'],
                    'dc_publisher' => ['Urban Planning Department'],
                    'dc_format' => ['DOCX'],
                    'dc_identifier' => ['PROP-2024-003']
                ]),
                'workflow_state' => 'published',
                'created_at' => now()->subMonths(4),
                'updated_at' => now()->subMonths(4)
            ],

            // Images
            [
                'title' => 'Microscopic Analysis of Cellular Structures',
                'description' => 'High-resolution microscopic images and analysis of cellular structures in biological samples.',
                'file_type' => 'Image',
                'user_id' => $user->id,
                'collection_id' => $clinicalStudies->id,
                'metadata' => json_encode([
                    'dc_title' => ['Microscopic Analysis of Cellular Structures'],
                    'dc_creator' => ['Dr. Lisa Wang'],
                    'dc_subject' => ['Microscopy', 'Cellular Biology', 'Image Analysis'],
                    'dc_description' => ['High-resolution microscopic images and analysis of cellular structures.'],
                    'dc_date_issued' => ['2024-03-22'],
                    'dc_type' => ['Research Images'],
                    'dc_publisher' => ['Biology Research Center'],
                    'dc_format' => ['JPG'],
                    'dc_identifier' => ['IMG-2024-004']
                ]),
                'workflow_state' => 'published',
                'created_at' => now()->subMonths(5),
                'updated_at' => now()->subMonths(5)
            ],

            // Datasets
            [
                'title' => 'Climate Change Data Analysis 2020-2024',
                'description' => 'Comprehensive dataset of climate change indicators and environmental measurements from 2020 to 2024.',
                'file_type' => 'Dataset',
                'user_id' => $user->id,
                'collection_id' => $datasets->id,
                'metadata' => json_encode([
                    'dc_title' => ['Climate Change Data Analysis 2020-2024'],
                    'dc_creator' => ['Climate Research Team'],
                    'dc_subject' => ['Climate Change', 'Environmental Data', 'Dataset'],
                    'dc_description' => ['Comprehensive dataset of climate change indicators and environmental measurements.'],
                    'dc_date_issued' => ['2024-02-18'],
                    'dc_type' => ['Dataset'],
                    'dc_publisher' => ['Environmental Research Institute'],
                    'dc_format' => ['CSV'],
                    'dc_identifier' => ['DATA-2024-005']
                ]),
                'workflow_state' => 'published',
                'created_at' => now()->subMonths(6),
                'updated_at' => now()->subMonths(6)
            ],

            // Videos
            [
                'title' => 'Surgical Procedure Demonstration: Advanced Techniques',
                'description' => 'Instructional video demonstrating advanced surgical procedures and techniques for medical training.',
                'file_type' => 'Video',
                'user_id' => $user->id,
                'collection_id' => $clinicalStudies->id,
                'metadata' => json_encode([
                    'dc_title' => ['Surgical Procedure Demonstration: Advanced Techniques'],
                    'dc_creator' => ['Dr. James Wilson', 'Medical Training Department'],
                    'dc_subject' => ['Surgical Training', 'Medical Education', 'Video Demonstration'],
                    'dc_description' => ['Instructional video demonstrating advanced surgical procedures and techniques.'],
                    'dc_date_issued' => ['2024-01-15'],
                    'dc_type' => ['Training Video'],
                    'dc_publisher' => ['Medical School'],
                    'dc_format' => ['MP4'],
                    'dc_identifier' => ['VID-2024-006']
                ]),
                'workflow_state' => 'published',
                'created_at' => now()->subMonths(7),
                'updated_at' => now()->subMonths(7)
            ],

            // Mixed types for better statistics
            [
                'title' => 'Advanced Materials for Sustainable Construction',
                'description' => 'Research on novel construction materials with reduced environmental impact and enhanced durability.',
                'file_type' => 'PDF',
                'user_id' => $user->id,
                'collection_id' => $researchPapers->id,
                'metadata' => json_encode([
                    'dc_title' => ['Advanced Materials for Sustainable Construction'],
                    'dc_creator' => ['Prof. Robert Wilson', 'Lisa Zhang'],
                    'dc_subject' => ['Materials Science', 'Sustainable Construction', 'Green Building'],
                    'dc_description' => ['Research on novel construction materials with reduced environmental impact.'],
                    'dc_date_issued' => ['2024-07-10'],
                    'dc_type' => ['Research Paper'],
                    'dc_publisher' => ['Materials Research Journal'],
                    'dc_format' => ['PDF'],
                    'dc_identifier' => ['RP-2024-007']
                ]),
                'workflow_state' => 'published',
                'created_at' => now()->subMonths(1),
                'updated_at' => now()->subMonths(1)
            ],
            [
                'title' => 'Literary Analysis of Modern Poetry Collections',
                'description' => 'Comprehensive analysis of contemporary poetry and literary techniques in modern collections.',
                'file_type' => 'Word Document',
                'user_id' => $user->id,
                'collection_id' => $literaryWorks->id,
                'metadata' => json_encode([
                    'dc_title' => ['Literary Analysis of Modern Poetry Collections'],
                    'dc_creator' => ['Dr. Elizabeth Wong'],
                    'dc_subject' => ['Literary Analysis', 'Modern Poetry', 'Literature'],
                    'dc_description' => ['Comprehensive analysis of contemporary poetry and literary techniques.'],
                    'dc_date_issued' => ['2024-08-05'],
                    'dc_type' => ['Literary Analysis'],
                    'dc_publisher' => ['Literature Department'],
                    'dc_format' => ['DOCX'],
                    'dc_identifier' => ['LIT-2024-008']
                ]),
                'workflow_state' => 'published',
                'created_at' => now()->subDays(15),
                'updated_at' => now()->subDays(15)
            ],
            [
                'title' => 'Clinical Trial Data: Cardiovascular Treatments',
                'description' => 'Dataset from clinical trials investigating new cardiovascular treatments and patient outcomes.',
                'file_type' => 'Dataset',
                'user_id' => $user->id,
                'collection_id' => $clinicalStudies->id,
                'metadata' => json_encode([
                    'dc_title' => ['Clinical Trial Data: Cardiovascular Treatments'],
                    'dc_creator' => ['Cardiology Research Team'],
                    'dc_subject' => ['Clinical Trials', 'Cardiovascular', 'Medical Data'],
                    'dc_description' => ['Dataset from clinical trials investigating new cardiovascular treatments.'],
                    'dc_date_issued' => ['2024-09-12'],
                    'dc_type' => ['Clinical Data'],
                    'dc_publisher' => ['Medical Research Center'],
                    'dc_format' => ['XLSX'],
                    'dc_identifier' => ['CLIN-2024-009']
                ]),
                'workflow_state' => 'published',
                'created_at' => now()->subDays(8),
                'updated_at' => now()->subDays(8)
            ],

            // Draft and Pending items
            [
                'title' => 'Quantum Computing Applications in Chemistry - Draft',
                'description' => 'Applications of quantum computing in molecular modeling and chemical simulations for drug discovery.',
                'file_type' => 'PDF',
                'user_id' => $user->id,
                'collection_id' => $journalArticles->id,
                'metadata' => json_encode([
                    'dc_title' => ['Quantum Computing Applications in Chemistry'],
                    'dc_creator' => ['Dr. Amanda Lee', 'David Kim'],
                    'dc_subject' => ['Quantum Computing', 'Computational Chemistry', 'Quantum Algorithms'],
                    'dc_description' => ['Applications of quantum computing in molecular modeling and chemical simulations.'],
                    'dc_date_issued' => ['2024-10-01'],
                    'dc_type' => ['Journal Article'],
                    'dc_publisher' => ['Journal of Computational Chemistry'],
                    'dc_format' => ['PDF'],
                    'dc_identifier' => ['JA-2024-010']
                ]),
                'workflow_state' => 'draft',
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subDays(3)
            ],
            [
                'title' => 'Artificial Intelligence in Creative Writing - Under Review',
                'description' => 'Exploring the role of AI tools in assisting and enhancing creative writing processes.',
                'file_type' => 'Word Document',
                'user_id' => $user->id,
                'collection_id' => $literaryWorks->id,
                'metadata' => json_encode([
                    'dc_title' => ['Artificial Intelligence in Creative Writing'],
                    'dc_creator' => ['Thomas Reed'],
                    'dc_subject' => ['Artificial Intelligence', 'Creative Writing', 'Digital Literature'],
                    'dc_description' => ['Exploring the role of AI tools in assisting creative writing processes.'],
                    'dc_date_issued' => ['2024-10-05'],
                    'dc_type' => ['Research Paper'],
                    'dc_publisher' => ['Digital Humanities Quarterly'],
                    'dc_format' => ['DOCX'],
                    'dc_identifier' => ['AI-2024-011']
                ]),
                'workflow_state' => 'pending_review',
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subDays(1)
            ]
        ];

        foreach ($items as $itemData) {
            Item::create($itemData);
        }

        $this->command->info('Demo data seeded successfully!');
        $this->command->info('Communities created: ' . Community::count());
        $this->command->info('Collections created: ' . Collection::count());
        $this->command->info('Items created: ' . Item::count());
        $this->command->info('---');
        $this->command->info('File Type Distribution:');
        $fileTypes = Item::select('file_type', DB::raw('count(*) as count'))
                        ->groupBy('file_type')
                        ->get();
        foreach ($fileTypes as $type) {
            $this->command->info("  {$type->file_type}: {$type->count} items");
        }
        $this->command->info('---');
        $this->command->info('Workflow State Distribution:');
        $states = Item::select('workflow_state', DB::raw('count(*) as count'))
                     ->groupBy('workflow_state')
                     ->get();
        foreach ($states as $state) {
            $this->command->info("  {$state->workflow_state}: {$state->count} items");
        }
    }
}