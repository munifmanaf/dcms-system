<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Community;
use App\Models\Collection;
use App\Models\Item;
use App\Models\Category;
use App\Models\Bitstream;
use App\Models\BitstreamFormat;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create users if they don't exist
        $admin = User::firstOrCreate(
            ['email' => 'admin@dcms.test'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        $manager = User::firstOrCreate(
            ['email' => 'manager@dcms.test'],
            [
                'name' => 'Content Manager', 
                'password' => Hash::make('password'),
                'role' => 'manager',
                'email_verified_at' => now(),
            ]
        );

        $user = User::firstOrCreate(
            ['email' => 'user@dcms.test'],
            [
                'name' => 'Regular User',
                'password' => Hash::make('password'), 
                'role' => 'user',
                'email_verified_at' => now(),
            ]
        );

        $reviewer = User::firstOrCreate(
            ['email' => 'reviewer@dcms.test'],
            [
                'name' => 'Reviewer User',
                'password' => Hash::make('password'),
                'role' => 'reviewer', 
                'email_verified_at' => now(),
            ]
        );

        // Create categories
        $categories = [
            ['name' => 'Research Papers', 'description' => 'Academic research papers and publications'],
            ['name' => 'Technical Reports', 'description' => 'Technical documentation and reports'],
            ['name' => 'Meeting Minutes', 'description' => 'Records of meetings and discussions'],
            ['name' => 'Policies', 'description' => 'Organizational policies and procedures'],
            ['name' => 'Templates', 'description' => 'Document templates and forms'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['name' => $category['name']],
                $category
            );
        }

        // Create communities
        $researchCommunity = Community::firstOrCreate(
            ['name' => 'Research Publications'],
            [
                'description' => 'Academic and research publications',
                'is_public' => true,
            ]
        );

        $hrCommunity = Community::firstOrCreate(
            ['name' => 'Human Resources'],
            [
                'description' => 'HR documents and policies', 
                'is_public' => true,
            ]
        );

        $techCommunity = Community::firstOrCreate(
            ['name' => 'Technical Documentation'],
            [
                'description' => 'Technical guides and documentation',
                'is_public' => true,
            ]
        );

        // Create collections within communities
        $journalCollection = Collection::firstOrCreate(
            ['name' => 'Journal Articles'],
            [
                'community_id' => $researchCommunity->id,
                'description' => 'Peer-reviewed journal articles',
                'is_public' => true,
            ]
        );

        $conferenceCollection = Collection::firstOrCreate(
            ['name' => 'Conference Papers'],
            [
                'community_id' => $researchCommunity->id,
                'description' => 'Conference proceedings and papers',
                'is_public' => true,
            ]
        );

        $policyCollection = Collection::firstOrCreate(
            ['name' => 'Company Policies'],
            [
                'community_id' => $hrCommunity->id,
                'description' => 'Official company policies and guidelines',
                'is_public' => false, // Internal only
            ]
        );

        $manualCollection = Collection::firstOrCreate(
            ['name' => 'Technical Manuals'],
            [
                'community_id' => $techCommunity->id,
                'description' => 'Technical manuals and guides',
                'is_public' => true,
            ]
        );

        
        // Create sample items with different workflow states
        $sampleItems = [
            // Draft items
            [
                'title' => 'Machine Learning Approaches for Document Classification',
                'content' => 'This research paper explores various machine learning techniques for automatic document classification in digital repositories.',
                'user_id' => $user->id,
                'collection_id' => $journalCollection->id,
                'workflow_state' => 'draft',
                'version_notes' => 'Initial draft version',
            ],
            [
                'title' => 'Quarterly Performance Review Process',
                'content' => 'Detailed guidelines for conducting quarterly performance reviews across all departments.',
                'user_id' => $manager->id, 
                'collection_id' => $policyCollection->id,
                'workflow_state' => 'draft',
                'version_notes' => 'Draft for internal review',
            ],

            // Submitted items
            [
                'title' => 'Digital Preservation Strategies for Institutional Repositories',
                'content' => 'Analysis of digital preservation strategies and their implementation in institutional repository systems.',
                'user_id' => $user->id,
                'collection_id' => $conferenceCollection->id,
                'workflow_state' => 'submitted',
                'submitted_at' => now()->subDays(2),
                'version_notes' => 'Submitted for technical review',
            ],
            [
                'title' => 'API Documentation for DCMS System',
                'content' => 'Complete API documentation for the Document Content Management System REST endpoints.',
                'user_id' => $manager->id,
                'collection_id' => $manualCollection->id, 
                'workflow_state' => 'submitted',
                'submitted_at' => now()->subDay(),
                'version_notes' => 'Ready for content review',
            ],

            // Under review items
            [
                'title' => 'Impact of Open Access on Research Visibility',
                'content' => 'Study examining how open access publishing affects research citation rates and visibility.',
                'user_id' => $user->id,
                'collection_id' => $journalCollection->id,
                'workflow_state' => 'under_review',
                'submitted_at' => now()->subDays(5),
                'version_notes' => 'Undergoing peer review',
            ],

            // Published items
            [
                'title' => 'DCMS User Guide and Manual',
                'content' => 'Complete user guide for the Document Content Management System, covering all features and functionality.',
                'user_id' => $admin->id,
                'collection_id' => $manualCollection->id,
                'workflow_state' => 'published', 
                'submitted_at' => now()->subDays(10),
                'published_at' => now()->subDays(7),
                'view_count' => 45,
                'download_count' => 23,
                'version_notes' => 'First published version',
            ],
            [
                'title' => 'Employee Code of Conduct',
                'content' => 'Official code of conduct policy for all employees, covering professional behavior and ethics.',
                'user_id' => $manager->id,
                'collection_id' => $policyCollection->id,
                'workflow_state' => 'published',
                'submitted_at' => now()->subDays(15),
                'published_at' => now()->subDays(12),
                'view_count' => 89,
                'download_count' => 67,
                'version_notes' => 'Approved by legal department',
            ],
        ];

        foreach ($sampleItems as $itemData) {
            $item = Item::firstOrCreate(
                ['title' => $itemData['title']],
                $itemData
            );

            // Assign random categories to items
            $randomCategories = Category::inRandomOrder()->limit(rand(1, 3))->get();
            $item->categories()->sync($randomCategories);
        }

        // Create some sample workflow actions for submitted items
        $submittedItems = Item::where('workflow_state', 'submitted')->get();
        $submitStep = \App\Models\WorkflowStep::where('action', 'submit')->first();

        foreach ($submittedItems as $item) {
            \App\Models\WorkflowAction::firstOrCreate(
                [
                    'item_id' => $item->id,
                    'action' => 'submit'
                ],
                [
                    'user_id' => $item->user_id,
                    'workflow_step_id' => $submitStep->id,
                    'comments' => 'Submitted for review process',
                    'status' => 'approved',
                    'metadata' => ['submitted_at' => $item->submitted_at->toISOString()],
                    'created_at' => $item->submitted_at,
                ]
            );
        }

        // ... existing code ...

        // Create sample bitstream formats if they don't exist
        $formats = [
            [
                'mimetype' => 'application/pdf',
                'short_description' => 'Adobe PDF',
                'description' => 'Portable Document Format',
                'support_level' => 'SUPPORTED',
                'extensions' => 'pdf'
            ],
            [
                'mimetype' => 'application/msword',
                'short_description' => 'Microsoft Word',
                'description' => 'Microsoft Word Document', 
                'support_level' => 'SUPPORTED',
                'extensions' => 'doc,docx'
            ],
            [
                'mimetype' => 'text/plain', 
                'short_description' => 'Plain Text',
                'description' => 'Plain Text File',
                'support_level' => 'SUPPORTED',
                'extensions' => 'txt'
            ],
            [
                'mimetype' => 'image/jpeg',
                'short_description' => 'JPEG Image',
                'description' => 'JPEG Image File',
                'support_level' => 'SUPPORTED', 
                'extensions' => 'jpg,jpeg'
            ]
        ];

        foreach ($formats as $format) {
            BitstreamFormat::firstOrCreate(
                ['mimetype' => $format['mimetype']],
                $format
            );
        }

        // Create sample bitstreams for items
        $items = Item::all();
        $pdfFormat = BitstreamFormat::where('mimetype', 'application/pdf')->first();
        $docFormat = BitstreamFormat::where('mimetype', 'application/msword')->first();
        $textFormat = BitstreamFormat::where('mimetype', 'text/plain')->first();

        foreach ($items as $item) {
            // Create 1-3 random bitstreams for each item
            $bitstreamCount = rand(1, 3);
            
            for ($i = 0; $i < $bitstreamCount; $i++) {
                $fileTypes = [
                    ['name' => 'research_paper.pdf', 'format' => $pdfFormat, 'size' => 2048000],
                    ['name' => 'technical_report.docx', 'format' => $docFormat, 'size' => 1536000],
                    ['name' => 'readme.txt', 'format' => $textFormat, 'size' => 10240],
                    ['name' => 'methodology.pdf', 'format' => $pdfFormat, 'size' => 3072000],
                    ['name' => 'data_analysis.docx', 'format' => $docFormat, 'size' => 2560000],
                ];
                
                $fileType = $fileTypes[array_rand($fileTypes)];
                $extension = pathinfo($fileType['name'], PATHINFO_EXTENSION);
                
                Bitstream::firstOrCreate(
                    [
                        'item_id' => $item->id,
                        'name' => $fileType['name']
                    ],
                    [
                        'name' => $fileType['name'],
                        'original_filename' => $fileType['name'],
                        'internal_id' => 'sample_' . Str::random(20),
                        'mime_type' => $fileType['format']->mimetype,
                        'file_extension' => $extension,
                        'size_bytes' => $fileType['size'],
                        'checksum' => md5($item->id . $fileType['name'] . time()),
                        'checksum_algorithm' => 'MD5',
                        'sequence_id' => $i,
                        'bitstream_format_id' => $fileType['format']->id,
                        'bundle_name' => 'ORIGINAL',
                        'description' => 'Sample file for ' . $item->title,
                        'is_current' => true,
                        'file_version' => 1,
                        'technical_metadata' => [
                            'sample_data' => true,
                            'generated_at' => now()->toISOString(),
                            'file_type' => $extension,
                        ]
                    ]
                );
            }
        }

        // ... rest of existing code (workflow actions) ...

        $this->command->info('Sample data created successfully!');
        $this->command->info('Users:');
        $this->command->info('- Admin: admin@dcms.test / password');
        $this->command->info('- Manager: manager@dcms.test / password'); 
        $this->command->info('- User: user@dcms.test / password');
        $this->command->info('- Reviewer: reviewer@dcms.test / password');
        $this->command->info('');
        $this->command->info('Items created with different workflow states:');
        $this->command->info('- 2 draft items (can test submission)');
        $this->command->info('- 2 submitted items (awaiting review)');
        $this->command->info('- 1 under review item');
        $this->command->info('- 2 published items');
    }
}
