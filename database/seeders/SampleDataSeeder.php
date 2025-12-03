<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Collection;
use App\Models\Item;
use App\Models\Community; // Add this
use Carbon\Carbon;

class SampleDataSeeder extends Seeder
{
    public function run()
    {
        // Clear existing data
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        DB::table('items')->truncate();
        // DB::table('collections')->truncate();
        // DB::table('communities')->truncate(); // Add this
        // DB::table('users')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');

        // Create Users with different roles (same as before)
        // $users = [
        //     [
        //         'name' => 'Admin User',
        //         'email' => 'admin@dcms.test',
        //         'password' => Hash::make('password'),
        //         'role' => 'admin',
        //         'email_verified_at' => now(),
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //     ],
        //     [
        //         'name' => 'Content Manager',
        //         'email' => 'manager@dcms.test',
        //         'password' => Hash::make('password'),
        //         'role' => 'manager',
        //         'email_verified_at' => now(),
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //     ],
        //     [
        //         'name' => 'Researcher One',
        //         'email' => 'researcher1@dcms.test',
        //         'password' => Hash::make('password'),
        //         'role' => 'user',
        //         'email_verified_at' => now(),
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //     ],
        //     [
        //         'name' => 'Researcher Two',
        //         'email' => 'researcher2@dcms.test',
        //         'password' => Hash::make('password'),
        //         'role' => 'user',
        //         'email_verified_at' => now(),
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //     ],
        //     [
        //         'name' => 'Student User',
        //         'email' => 'student@dcms.test',
        //         'password' => Hash::make('password'),
        //         'role' => 'user',
        //         'email_verified_at' => now(),
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //     ]
        // ];

        // foreach ($users as $user) {
        //     User::create($user);
        // }

        // $adminUser = User::where('email', 'admin@dcms.test')->first();
        // $managerUser = User::where('email', 'manager@dcms.test')->first();
        // $researcher1 = User::where('email', 'researcher1@dcms.test')->first();
        // $researcher2 = User::where('email', 'researcher2@dcms.test')->first();

        // // CREATE COMMUNITIES
        // $communities = [
        //     [
        //         'name' => 'Digital Humanities Research Group',
        //         'description' => 'A community for researchers working on digital humanities projects and computational approaches to cultural heritage.',
        //         // 'user_id' => $adminUser->id,
        //         'created_at' => now()->subMonths(8),
        //         'updated_at' => now()->subMonths(1),
        //     ],
        //     [
        //         'name' => 'Data Science Collaborators',
        //         'description' => 'Community for data scientists, statisticians, and researchers sharing datasets and analytical methods.',
        //         // 'user_id' => $managerUser->id,
        //         'created_at' => now()->subMonths(6),
        //         'updated_at' => now()->subWeeks(2),
        //     ],
        //     [
        //         'name' => 'Open Science Initiative',
        //         'description' => 'Promoting open access, open data, and reproducible research practices across disciplines.',
        //         // 'user_id' => $researcher1->id,
        //         'created_at' => now()->subMonths(4),
        //         'updated_at' => now()->subWeek(),
        //     ],
        //     [
        //         'name' => 'Historical Preservation Network',
        //         'description' => 'Community dedicated to preserving and digitizing historical documents and cultural artifacts.',
        //         // 'user_id' => $researcher2->id,
        //         'created_at' => now()->subMonths(3),
        //         'updated_at' => now()->subDays(3),
        //     ]
        // ];

        // foreach ($communities as $community) {
        //     Community::create($community);
        // }

        // $digitalHumanities = Community::where('name', 'Digital Humanities Research Group')->first();
        // $dataScience = Community::where('name', 'Data Science Collaborators')->first();
        // $openScience = Community::where('name', 'Open Science Initiative')->first();
        // $historicalPreservation = Community::where('name', 'Historical Preservation Network')->first();

        // // Create Collections (updated to include community_id if you have that relationship)
        // $collections = [
        //     [
        //         'name' => 'Academic Research Papers',
        //         'description' => 'Collection of peer-reviewed academic papers and research findings',
        //         // 'user_id' => $adminUser->id,
        //         'community_id' => $digitalHumanities->id,
        //         'created_at' => now()->subMonths(6),
        //         'updated_at' => now()->subMonths(1),
        //     ],
        //     [
        //         'name' => 'Historical Archives',
        //         'description' => 'Digital preservation of historical documents and archives',
        //         'community_id' => $historicalPreservation->id,
        //         'created_at' => now()->subMonths(4),
        //         'updated_at' => now()->subWeeks(2),
        //     ],
        //     [
        //         'name' => 'Scientific Data Sets',
        //         'description' => 'Research data sets and scientific measurements',
        //         'community_id' => $dataScience->id,
        //         'created_at' => now()->subMonths(3),
        //         'updated_at' => now()->subWeek(),
        //     ],
        //     [
        //         'name' => 'Multimedia Resources',
        //         'description' => 'Educational videos, images, and audio resources',
        //         'community_id' => $digitalHumanities->id,
        //         'created_at' => now()->subMonths(2),
        //         'updated_at' => now()->subDays(3),
        //     ],
        //     [
        //         'name' => 'Conference Proceedings',
        //         'description' => 'Papers and presentations from academic conferences',
        //         'community_id' => $openScience->id,
        //         'created_at' => now()->subMonth(),
        //         'updated_at' => now()->subDays(1),
        //     ]
        // ];

        // foreach ($collections as $collection) {
        //     Collection::create($collection);
        // }

        // // Rest of your items seeding remains the same...
        // $researchCollection = Collection::where('name', 'Academic Research Papers')->first();
        // $historicalCollection = Collection::where('name', 'Historical Archives')->first();
        // $scienceCollection = Collection::where('name', 'Scientific Data Sets')->first();
        // $multimediaCollection = Collection::where('name', 'Multimedia Resources')->first();
        // $conferenceCollection = Collection::where('name', 'Conference Proceedings')->first();

        // // Your existing items array here (same as before)...
        // $items = [
        //     // Academic Research Papers - High download/views
        //     [
        //         'title' => 'Machine Learning Applications in Healthcare',
        //         'slug' => 'machine-learning-healthcare',
        //         'description' => 'Comprehensive study on ML applications in medical diagnosis and treatment planning',
        //         'content' => 'This research paper explores various machine learning algorithms...',
        //         'metadata' => json_encode([
        //             'dc_title' => ['Machine Learning Applications in Healthcare'],
        //             'dc_creator' => ['Dr. Sarah Chen', 'Prof. Michael Rodriguez'],
        //             'dc_subject' => ['Machine Learning', 'Healthcare', 'AI', 'Medical Diagnosis'],
        //             'dc_description' => ['Comprehensive study on ML applications in medical diagnosis'],
        //             'dc_publisher' => ['University Research Press'],
        //             'dc_date_issued' => ['2024-01-15'],
        //             'dc_type' => ['Research Paper'],
        //             'dc_format' => ['PDF'],
        //             'dc_identifier' => ['DOI:10.1234/ml-health-2024']
        //         ]),
        //         'file_path' => 'research/ml_healthcare.pdf',
        //         'file_name' => 'machine_learning_healthcare_research.pdf',
        //         'file_size' => '2.5 MB',
        //         'file_type' => 'PDF',
        //         'user_id' => $researcher1->id,
        //         'collection_id' => $researchCollection->id,
        //         'is_published' => true,
        //         'is_approved' => true,
        //         'approved_by' => $adminUser->id,
        //         'workflow_state' => 'published',
        //         'download_count' => 245,
        //         'view_count' => 567,
        //         'published_at' => now()->subMonths(3),
        //         'approved_at' => now()->subMonths(3),
        //         'last_downloaded_at' => now()->subDays(2),
        //         'last_viewed_at' => now()->subDay(),
        //         'created_at' => now()->subMonths(4),
        //         'updated_at' => now()->subDays(2),
        //     ],
        //     [
        //         'title' => 'Climate Change Impact on Coastal Ecosystems',
        //         'slug' => 'climate-change-coastal-ecosystems',
        //         'description' => 'Long-term study of climate change effects on marine biodiversity',
        //         'content' => 'This paper presents a 10-year longitudinal study...',
        //         'metadata' => json_encode([
        //             'dc_title' => ['Climate Change Impact on Coastal Ecosystems'],
        //             'dc_creator' => ['Dr. Emily Watson', 'Dr. James Kim'],
        //             'dc_subject' => ['Climate Change', 'Marine Biology', 'Ecosystem', 'Biodiversity'],
        //             'dc_description' => ['Long-term study of climate change effects on marine biodiversity'],
        //             'dc_publisher' => ['Environmental Research Journal'],
        //             'dc_date_issued' => ['2024-02-20'],
        //             'dc_type' => ['Research Paper'],
        //             'dc_format' => ['PDF'],
        //             'dc_identifier' => ['DOI:10.1234/climate-coastal-2024']
        //         ]),
        //         'file_path' => 'research/climate_coastal.pdf',
        //         'file_name' => 'climate_change_coastal_study.pdf',
        //         'file_size' => '3.1 MB',
        //         'file_type' => 'PDF',
        //         'user_id' => $researcher2->id,
        //         'collection_id' => $researchCollection->id,
        //         'is_published' => true,
        //         'is_approved' => true,
        //         'approved_by' => $managerUser->id,
        //         'workflow_state' => 'published',
        //         'download_count' => 189,
        //         'view_count' => 432,
        //         'published_at' => now()->subMonths(2),
        //         'approved_at' => now()->subMonths(2),
        //         'last_downloaded_at' => now()->subDays(5),
        //         'last_viewed_at' => now()->subDays(1),
        //         'created_at' => now()->subMonths(3),
        //         'updated_at' => now()->subDays(5),
        //     ],

        //     // Historical Archives - Medium traffic
        //     [
        //         'title' => '19th Century Trade Documents Collection',
        //         'slug' => '19th-century-trade-documents',
        //         'description' => 'Digitized collection of trade agreements and commercial documents from 1800s',
        //         'content' => 'This collection contains scanned documents from various trade organizations...',
        //         'metadata' => json_encode([
        //             'dc_title' => ['19th Century Trade Documents Collection'],
        //             'dc_creator' => ['Historical Archives Department'],
        //             'dc_subject' => ['History', 'Trade', 'Commerce', '19th Century', 'Documents'],
        //             'dc_description' => ['Digitized collection of trade agreements and commercial documents'],
        //             'dc_publisher' => ['National Archives'],
        //             'dc_date_issued' => ['2024-01-10'],
        //             'dc_type' => ['Archival Collection'],
        //             'dc_format' => ['ZIP'],
        //             'dc_identifier' => ['ARCH-2024-TRADE-001']
        //         ]),
        //         'file_path' => 'archives/trade_documents.zip',
        //         'file_name' => '19th_century_trade_collection.zip',
        //         'file_size' => '45.2 MB',
        //         'file_type' => 'ZIP',
        //         'user_id' => $managerUser->id,
        //         'collection_id' => $historicalCollection->id,
        //         'is_published' => true,
        //         'is_approved' => true,
        //         'approved_by' => $adminUser->id,
        //         'workflow_state' => 'published',
        //         'download_count' => 87,
        //         'view_count' => 234,
        //         'published_at' => now()->subMonths(4),
        //         'approved_at' => now()->subMonths(4),
        //         'last_downloaded_at' => now()->subWeek(),
        //         'last_viewed_at' => now()->subDays(2),
        //         'created_at' => now()->subMonths(5),
        //         'updated_at' => now()->subWeek(),
        //     ],

        //     // Scientific Data Sets - Various file types
        //     [
        //         'title' => 'Genomic Sequencing Data - Human Genome Project',
        //         'slug' => 'genomic-sequencing-data',
        //         'description' => 'Raw genomic sequencing data from human genome research',
        //         'content' => 'This dataset contains complete genomic sequencing information...',
        //         'metadata' => json_encode([
        //             'dc_title' => ['Genomic Sequencing Data - Human Genome Project'],
        //             'dc_creator' => ['Genomics Research Team'],
        //             'dc_subject' => ['Genomics', 'DNA', 'Sequencing', 'Bioinformatics', 'Research Data'],
        //             'dc_description' => ['Raw genomic sequencing data from human genome research'],
        //             'dc_publisher' => ['Genomics Data Repository'],
        //             'dc_date_issued' => ['2024-03-05'],
        //             'dc_type' => ['Dataset'],
        //             'dc_format' => ['CSV'],
        //             'dc_identifier' => ['GDR-2024-GENOME-001']
        //         ]),
        //         'file_path' => 'datasets/genomic_data.csv',
        //         'file_name' => 'human_genome_sequencing.csv',
        //         'file_size' => '156.8 MB',
        //         'file_type' => 'CSV',
        //         'user_id' => $researcher1->id,
        //         'collection_id' => $scienceCollection->id,
        //         'is_published' => true,
        //         'is_approved' => true,
        //         'approved_by' => $managerUser->id,
        //         'workflow_state' => 'published',
        //         'download_count' => 134,
        //         'view_count' => 298,
        //         'published_at' => now()->subMonths(2),
        //         'approved_at' => now()->subMonths(2),
        //         'last_downloaded_at' => now()->subDays(3),
        //         'last_viewed_at' => now()->subDay(),
        //         'created_at' => now()->subMonths(3),
        //         'updated_at' => now()->subDays(3),
        //     ],
        //     [
        //         'title' => 'Astronomical Observation Data - Exoplanet Research',
        //         'slug' => 'astronomical-observation-exoplanet',
        //         'description' => 'Telescope observation data for exoplanet detection and analysis',
        //         'content' => 'This dataset contains photometric and spectroscopic data...',
        //         'metadata' => json_encode([
        //             'dc_title' => ['Astronomical Observation Data - Exoplanet Research'],
        //             'dc_creator' => ['Space Observatory Team'],
        //             'dc_subject' => ['Astronomy', 'Exoplanets', 'Telescope', 'Observation', 'Space'],
        //             'dc_description' => ['Telescope observation data for exoplanet detection and analysis'],
        //             'dc_publisher' => ['Space Research Institute'],
        //             'dc_date_issued' => ['2024-02-28'],
        //             'dc_type' => ['Dataset'],
        //             'dc_format' => ['FITS'],
        //             'dc_identifier' => ['SRI-2024-EXOPLANET-001']
        //         ]),
        //         'file_path' => 'datasets/exoplanet_observations.fits',
        //         'file_name' => 'exoplanet_research_data.fits',
        //         'file_size' => '89.3 MB',
        //         'file_type' => 'FITS',
        //         'user_id' => $researcher2->id,
        //         'collection_id' => $scienceCollection->id,
        //         'is_published' => true,
        //         'is_approved' => true,
        //         'approved_by' => $adminUser->id,
        //         'workflow_state' => 'published',
        //         'download_count' => 76,
        //         'view_count' => 187,
        //         'published_at' => now()->subMonths(1),
        //         'approved_at' => now()->subMonths(1),
        //         'last_downloaded_at' => now()->subDays(7),
        //         'last_viewed_at' => now()->subDays(2),
        //         'created_at' => now()->subMonths(2),
        //         'updated_at' => now()->subDays(7),
        //     ],

        //     // Multimedia Resources - Different file types
        //     [
        //         'title' => 'Educational Video: Introduction to Quantum Computing',
        //         'slug' => 'quantum-computing-video',
        //         'description' => 'Comprehensive video tutorial explaining quantum computing fundamentals',
        //         'content' => 'This video covers quantum bits, superposition, entanglement...',
        //         'metadata' => json_encode([
        //             'dc_title' => ['Educational Video: Introduction to Quantum Computing'],
        //             'dc_creator' => ['Dr. Alex Thompson'],
        //             'dc_subject' => ['Quantum Computing', 'Physics', 'Education', 'Video', 'Tutorial'],
        //             'dc_description' => ['Comprehensive video tutorial explaining quantum computing fundamentals'],
        //             'dc_publisher' => ['Science Education Channel'],
        //             'dc_date_issued' => ['2024-03-15'],
        //             'dc_type' => ['Video'],
        //             'dc_format' => ['MP4'],
        //             'dc_identifier' => ['SEC-2024-QUANTUM-VIDEO']
        //         ]),
        //         'file_path' => 'multimedia/quantum_computing.mp4',
        //         'file_name' => 'quantum_computing_introduction.mp4',
        //         'file_size' => '245.7 MB',
        //         'file_type' => 'MP4',
        //         'user_id' => $managerUser->id,
        //         'collection_id' => $multimediaCollection->id,
        //         'is_published' => true,
        //         'is_approved' => true,
        //         'approved_by' => $adminUser->id,
        //         'workflow_state' => 'published',
        //         'download_count' => 156,
        //         'view_count' => 423,
        //         'published_at' => now()->subMonth(),
        //         'approved_at' => now()->subMonth(),
        //         'last_downloaded_at' => now()->subDays(1),
        //         'last_viewed_at' => now()->subHours(6),
        //         'created_at' => now()->subMonths(2),
        //         'updated_at' => now()->subDays(1),
        //     ],
        //     [
        //         'title' => 'High-Resolution Microscopy Images - Cell Biology',
        //         'slug' => 'microscopy-images-cell-biology',
        //         'description' => 'Collection of high-resolution microscopy images for cell biology research',
        //         'content' => 'This collection includes various cell structures and organelles...',
        //         'metadata' => json_encode([
        //             'dc_title' => ['High-Resolution Microscopy Images - Cell Biology'],
        //             'dc_creator' => ['Cell Biology Research Group'],
        //             'dc_subject' => ['Microscopy', 'Cell Biology', 'Images', 'Research', 'Science'],
        //             'dc_description' => ['Collection of high-resolution microscopy images for cell biology research'],
        //             'dc_publisher' => ['Biological Imaging Center'],
        //             'dc_date_issued' => ['2024-02-10'],
        //             'dc_type' => ['Image Collection'],
        //             'dc_format' => ['TIFF'],
        //             'dc_identifier' => ['BIC-2024-CELL-IMAGES']
        //         ]),
        //         'file_path' => 'multimedia/microscopy_images.zip',
        //         'file_name' => 'cell_biology_microscopy.zip',
        //         'file_size' => '367.2 MB',
        //         'file_type' => 'ZIP',
        //         'user_id' => $researcher1->id,
        //         'collection_id' => $multimediaCollection->id,
        //         'is_published' => true,
        //         'is_approved' => true,
        //         'approved_by' => $managerUser->id,
        //         'workflow_state' => 'published',
        //         'download_count' => 92,
        //         'view_count' => 256,
        //         'published_at' => now()->subMonths(2),
        //         'approved_at' => now()->subMonths(2),
        //         'last_downloaded_at' => now()->subDays(4),
        //         'last_viewed_at' => now()->subDays(1),
        //         'created_at' => now()->subMonths(3),
        //         'updated_at' => now()->subDays(4),
        //     ],

        //     // Conference Proceedings - Mixed content
        //     [
        //         'title' => 'International AI Conference 2024 - Proceedings',
        //         'slug' => 'ai-conference-2024-proceedings',
        //         'description' => 'Complete proceedings from the International AI Conference 2024',
        //         'content' => 'This document contains all papers, presentations, and discussions...',
        //         'metadata' => json_encode([
        //             'dc_title' => ['International AI Conference 2024 - Proceedings'],
        //             'dc_creator' => ['AI Conference Committee'],
        //             'dc_subject' => ['Artificial Intelligence', 'Conference', 'Proceedings', 'Research', 'Technology'],
        //             'dc_description' => ['Complete proceedings from the International AI Conference 2024'],
        //             'dc_publisher' => ['AI Research Foundation'],
        //             'dc_date_issued' => ['2024-03-20'],
        //             'dc_type' => ['Conference Proceedings'],
        //             'dc_format' => ['PDF'],
        //             'dc_identifier' => ['AIC-2024-PROCEEDINGS']
        //         ]),
        //         'file_path' => 'conference/ai_2024_proceedings.pdf',
        //         'file_name' => 'ai_conference_2024_proceedings.pdf',
        //         'file_size' => '15.8 MB',
        //         'file_type' => 'PDF',
        //         'user_id' => $researcher2->id,
        //         'collection_id' => $conferenceCollection->id,
        //         'is_published' => true,
        //         'is_approved' => true,
        //         'approved_by' => $adminUser->id,
        //         'workflow_state' => 'published',
        //         'download_count' => 203,
        //         'view_count' => 478,
        //         'published_at' => now()->subWeeks(2),
        //         'approved_at' => now()->subWeeks(2),
        //         'last_downloaded_at' => now()->subDays(1),
        //         'last_viewed_at' => now()->subHours(3),
        //         'created_at' => now()->subMonth(),
        //         'updated_at' => now()->subDays(1),
        //     ],

        //     // Add some items with different workflow states for testing
        //     [
        //         'title' => 'Blockchain Technology in Supply Chain Management - DRAFT',
        //         'slug' => 'blockchain-supply-chain-draft',
        //         'description' => 'Research on blockchain applications in supply chain optimization (Work in Progress)',
        //         'content' => 'This paper explores how blockchain technology can enhance supply chain transparency...',
        //         'metadata' => json_encode([
        //             'dc_title' => ['Blockchain Technology in Supply Chain Management'],
        //             'dc_creator' => ['Dr. Robert Chen'],
        //             'dc_subject' => ['Blockchain', 'Supply Chain', 'Technology', 'Logistics'],
        //             'dc_description' => ['Research on blockchain applications in supply chain optimization'],
        //             'dc_publisher' => ['Technology Research Institute'],
        //             'dc_date_issued' => ['2024-03-25'],
        //             'dc_type' => ['Research Paper'],
        //             'dc_format' => ['PDF']
        //         ]),
        //         'file_path' => 'research/blockchain_supply_chain.pdf',
        //         'file_name' => 'blockchain_supply_chain_draft.pdf',
        //         'file_size' => '1.8 MB',
        //         'file_type' => 'PDF',
        //         'user_id' => $researcher1->id,
        //         'collection_id' => $researchCollection->id,
        //         'is_published' => false,
        //         'is_approved' => false,
        //         'workflow_state' => 'draft',
        //         'download_count' => 0,
        //         'view_count' => 12,
        //         'created_at' => now()->subWeek(),
        //         'updated_at' => now()->subDays(2),
        //     ],
        //     [
        //         'title' => 'Renewable Energy Storage Solutions - UNDER REVIEW',
        //         'slug' => 'renewable-energy-storage-review',
        //         'description' => 'Analysis of advanced energy storage technologies for renewable systems',
        //         'content' => 'This research evaluates various energy storage methods...',
        //         'metadata' => json_encode([
        //             'dc_title' => ['Renewable Energy Storage Solutions'],
        //             'dc_creator' => ['Dr. Maria Gonzalez'],
        //             'dc_subject' => ['Renewable Energy', 'Energy Storage', 'Sustainability', 'Technology'],
        //             'dc_description' => ['Analysis of advanced energy storage technologies for renewable systems'],
        //             'dc_publisher' => ['Energy Research Journal'],
        //             'dc_date_issued' => ['2024-03-18'],
        //             'dc_type' => ['Research Paper'],
        //             'dc_format' => ['PDF']
        //         ]),
        //         'file_path' => 'research/energy_storage.pdf',
        //         'file_name' => 'renewable_energy_storage.pdf',
        //         'file_size' => '2.2 MB',
        //         'file_type' => 'PDF',
        //         'user_id' => $researcher2->id,
        //         'collection_id' => $researchCollection->id,
        //         'is_published' => false,
        //         'is_approved' => false,
        //         'workflow_state' => 'pending_review',
        //         'download_count' => 0,
        //         'view_count' => 8,
        //         'submitted_at' => now()->subDays(5),
        //         'created_at' => now()->subWeeks(2),
        //         'updated_at' => now()->subDays(5),
        //     ]
        // ];

        // foreach ($items as $item) {
        //     Item::create($item);
        // }

        // $this->command->info('Demo data seeded successfully!');
        // $this->command->info('Total Users: ' . User::count());
        // $this->command->info('Total Communities: ' . Community::count());
        // $this->command->info('Total Collections: ' . Collection::count());
        // $this->command->info('Total Items: ' . Item::count());
        // $this->command->info('Total Downloads: ' . Item::sum('download_count'));
        // $this->command->info('Total Views: ' . Item::sum('view_count'));
    }
}

