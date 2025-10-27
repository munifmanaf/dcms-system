<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\Collection;
use App\Models\Category;
use App\Models\Community;
use App\Models\User;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ItemTestDataSeeder extends Seeder
{
    public function run(): void
    {
        // Get or create users
        $admin = User::where('email', 'admin@test.com')->first();
        $contentManager = User::where('email', 'content@test.com')->first();
        $technicalReviewer = User::where('email', 'technical@test.com')->first();
        $regularUser = User::where('email', 'user@test.com')->first();

        // Create test community and collection
        $community = Community::firstOrCreate(
            ['name' => 'Digital Library'],
            ['description' => 'Community for digital content management']
        );

        $collection = Collection::firstOrCreate(
            ['name' => 'Research Papers'],
            [
                'description' => 'Collection of academic research papers',
                'community_id' => $community->id
            ]
        );

        // Create categories
        $categories = [
            Category::firstOrCreate(['name' => 'Academic']),
            Category::firstOrCreate(['name' => 'Technical']),
            Category::firstOrCreate(['name' => 'Creative']),
            Category::firstOrCreate(['name' => 'Business']),
            Category::firstOrCreate(['name' => 'Educational']),
            Category::firstOrCreate(['name' => 'Scientific']),
        ];

        // Clear existing test items
        Item::where('title', 'LIKE', 'Test Item%')->delete();

        // ============================================================================
        // ITEM 1: DRAFT STAGE - Basic user submission
        // ============================================================================
        $draftItem = Item::create([
            'title' => 'Test Item - Draft Research Paper',
            'description' => 'A research paper on artificial intelligence applications in healthcare. This is in initial draft stage awaiting submission.',
            'collection_id' => $collection->id,
            'user_id' => $regularUser->id,
            'is_published' => false,
            'is_approved' => false,
            'is_archived' => false,
            'is_featured' => false,
            'approved_by' => null,
            'approved_at' => null,
            'metadata' => json_encode([
                'document_type' => 'research_paper',
                'author' => 'Dr. Sarah Chen',
                'institution' => 'University of Technology',
                'word_count' => 8450,
                'references' => 32,
                'abstract' => 'This paper explores AI applications in diagnostic medicine...',
                'keywords' => ['AI', 'Healthcare', 'Machine Learning', 'Diagnostics'],
                'sections' => ['introduction', 'methodology', 'results', 'discussion', 'conclusion'],
                'draft_version' => '1.2',
                'submission_date' => null
            ]),
            'file_path' => 'items/research_draft.pdf',
            'file_name' => 'ai_healthcare_research_draft.pdf',
            'file_size' => 2548000,
            'file_type' => 'application/pdf',
            'slug' => Str::slug('Test Item - Draft Research Paper'),
            'created_at' => Carbon::now()->subDays(5),
            'updated_at' => Carbon::now()->subDays(1),
        ]);
        $draftItem->categories()->attach([$categories[0]->id, $categories[1]->id, $categories[5]->id]);

        // ============================================================================
        // ITEM 2: SUBMITTED FOR REVIEW - Awaiting technical review
        // ============================================================================
        $submittedItem = Item::create([
            'title' => 'Test Item - Technical Documentation: API Integration',
            'description' => 'Comprehensive technical documentation for REST API integration with third-party services. Currently submitted for technical review.',
            'collection_id' => $collection->id,
            'user_id' => $contentManager->id,
            'is_published' => false,
            'is_approved' => false,
            'is_archived' => false,
            'is_featured' => false,
            'approved_by' => null,
            'approved_at' => null,
            'metadata' => json_encode([
                'document_type' => 'technical_documentation',
                'technology_stack' => ['PHP', 'Laravel', 'REST API', 'OAuth2'],
                'version' => '2.0.1',
                'review_status' => 'awaiting_technical_review',
                'complexity_level' => 'advanced',
                'estimated_read_time' => '45 minutes',
                'code_examples' => true,
                'diagrams_included' => true,
                'last_reviewed' => null,
                'reviewers_assigned' => ['technical_team']
            ]),
            'file_path' => 'items/api_documentation.pdf',
            'file_name' => 'rest_api_integration_guide_v2.pdf',
            'file_size' => 1876000,
            'file_type' => 'application/pdf',
            'slug' => Str::slug('Test Item - Technical Documentation API Integration'),
            'created_at' => Carbon::now()->subDays(3),
            'updated_at' => Carbon::now()->subHours(12),
        ]);
        $submittedItem->categories()->attach([$categories[1]->id, $categories[3]->id]);

        // ============================================================================
        // ITEM 3: IN TECHNICAL REVIEW - Being reviewed by technical team
        // ============================================================================
        $techReviewItem = Item::create([
            'title' => 'Test Item - Software Architecture Proposal',
            'description' => 'Proposal for microservices architecture migration with detailed technical specifications and implementation roadmap.',
            'collection_id' => $collection->id,
            'user_id' => $technicalReviewer->id,
            'is_published' => false,
            'is_approved' => false,
            'is_archived' => false,
            'is_featured' => false,
            'approved_by' => null,
            'approved_at' => null,
            'metadata' => json_encode([
                'document_type' => 'architecture_proposal',
                'current_architecture' => 'monolithic',
                'proposed_architecture' => 'microservices',
                'estimated_timeline' => '6 months',
                'team_size_required' => 8,
                'budget_estimate' => 150000,
                'risk_assessment' => 'medium',
                'technical_reviewer' => 'tech_team_lead',
                'review_start_date' => Carbon::now()->subDays(2)->format('Y-m-d'),
                'review_notes' => 'Initial review in progress, checking scalability aspects',
                'dependencies' => ['kubernetes', 'docker', 'ci_cd_pipeline']
            ]),
            'file_path' => 'items/architecture_proposal.docx',
            'file_name' => 'microservices_migration_proposal_v3.docx',
            'file_size' => 3245000,
            'file_type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'slug' => Str::slug('Test Item - Software Architecture Proposal'),
            'created_at' => Carbon::now()->subDays(7),
            'updated_at' => Carbon::now()->subHours(6),
        ]);
        $techReviewItem->categories()->attach([$categories[1]->id, $categories[3]->id, $categories[4]->id]);

        // ============================================================================
        // ITEM 4: IN CONTENT REVIEW - Content quality assessment
        // ============================================================================
        $contentReviewItem = Item::create([
            'title' => 'Test Item - Digital Marketing Strategy 2024',
            'description' => 'Comprehensive digital marketing strategy including SEO, social media, and content marketing plans for the upcoming year.',
            'collection_id' => $collection->id,
            'user_id' => $contentManager->id,
            'is_published' => false,
            'is_approved' => false,
            'is_archived' => false,
            'is_featured' => false,
            'approved_by' => null,
            'approved_at' => null,
            'metadata' => json_encode([
                'document_type' => 'marketing_strategy',
                'fiscal_year' => 2024,
                'departments_involved' => ['marketing', 'sales', 'product'],
                'budget_allocated' => 500000,
                'content_reviewer' => 'content_team_lead',
                'review_status' => 'content_review',
                'readability_score' => 8.2,
                'target_audience' => ['B2B', 'enterprise'],
                'campaigns_planned' => 12,
                'kpis' => ['leads', 'conversion_rate', 'roi'],
                'content_quality_notes' => 'Well-structured, needs more case studies'
            ]),
            'file_path' => 'items/marketing_strategy.pptx',
            'file_name' => '2024_digital_marketing_strategy_final.pptx',
            'file_size' => 4589000,
            'file_type' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'slug' => Str::slug('Test Item - Digital Marketing Strategy 2024'),
            'created_at' => Carbon::now()->subDays(4),
            'updated_at' => Carbon::now()->subHours(3),
        ]);
        $contentReviewItem->categories()->attach([$categories[3]->id, $categories[4]->id]);

        // ============================================================================
        // ITEM 5: APPROVED - Ready for publication
        // ============================================================================
        $approvedItem = Item::create([
            'title' => 'Test Item - Approved: Data Privacy Policy Update',
            'description' => 'Updated data privacy policy compliant with latest GDPR regulations and industry best practices.',
            'collection_id' => $collection->id,
            'user_id' => $admin->id,
            'is_published' => false,
            'is_approved' => true,
            'is_archived' => false,
            'is_featured' => false,
            'approved_by' => $admin->id,
            'approved_at' => Carbon::now()->subDays(1),
            'metadata' => json_encode([
                'document_type' => 'policy_document',
                'policy_version' => '3.1',
                'effective_date' => '2024-02-01',
                'compliance_standards' => ['GDPR', 'CCPA', 'ISO27001'],
                'approval_chain' => ['legal', 'compliance', 'executive'],
                'review_frequency' => 'quarterly',
                'last_review_date' => Carbon::now()->subMonths(3)->format('Y-m-d'),
                'next_review_date' => Carbon::now()->addMonths(3)->format('Y-m-d'),
                'change_log' => [
                    'v3.1' => 'Updated data retention policies',
                    'v3.0' => 'Added GDPR compliance section',
                    'v2.5' => 'Revised data breach procedures'
                ]
            ]),
            'file_path' => 'items/privacy_policy.pdf',
            'file_name' => 'data_privacy_policy_v3.1_approved.pdf',
            'file_size' => 1568000,
            'file_type' => 'application/pdf',
            'slug' => Str::slug('Test Item - Approved Data Privacy Policy Update'),
            'created_at' => Carbon::now()->subDays(10),
            'updated_at' => Carbon::now()->subDays(1),
        ]);
        $approvedItem->categories()->attach([$categories[3]->id, $categories[4]->id]);

        // ============================================================================
        // ITEM 6: PUBLISHED - Live content
        // ============================================================================
        $publishedItem = Item::create([
            'title' => 'Test Item - Published: Introduction to Machine Learning',
            'description' => 'Comprehensive guide to machine learning concepts, algorithms, and practical applications for beginners and intermediate learners.',
            'collection_id' => $collection->id,
            'user_id' => $contentManager->id,
            'is_published' => true,
            'is_approved' => true,
            'is_archived' => false,
            'is_featured' => true,
            'approved_by' => $admin->id,
            'approved_at' => Carbon::now()->subDays(3),
            'metadata' => json_encode([
                'document_type' => 'educational_content',
                'difficulty_level' => 'beginner',
                'learning_objectives' => [
                    'Understand ML fundamentals',
                    'Implement basic algorithms',
                    'Apply ML to real problems'
                ],
                'estimated_completion_time' => '8 hours',
                'prerequisites' => ['basic programming', 'linear algebra'],
                'resources_included' => ['code examples', 'datasets', 'quizzes'],
                'publication_date' => Carbon::now()->subDays(2)->format('Y-m-d'),
                'views_count' => 1247,
                'average_rating' => 4.8,
                'downloads_count' => 893
            ]),
            'file_path' => 'items/ml_guide.pdf',
            'file_name' => 'introduction_to_machine_learning_complete_guide.pdf',
            'file_size' => 5876000,
            'file_type' => 'application/pdf',
            'slug' => Str::slug('Test Item - Published Introduction to Machine Learning'),
            'created_at' => Carbon::now()->subDays(15),
            'updated_at' => Carbon::now()->subDays(2),
        ]);
        $publishedItem->categories()->attach([$categories[0]->id, $categories[1]->id, $categories[4]->id, $categories[5]->id]);

        // ============================================================================
        // ITEM 7: ARCHIVED - Historical content
        // ============================================================================
        $archivedItem = Item::create([
            'title' => 'Test Item - Archived: Legacy System Documentation',
            'description' => 'Documentation for legacy CRM system that has been decommissioned. Archived for historical reference only.',
            'collection_id' => $collection->id,
            'user_id' => $regularUser->id,
            'is_published' => false,
            'is_approved' => true,
            'is_archived' => true,
            'is_featured' => false,
            'approved_by' => $admin->id,
            'approved_at' => Carbon::now()->subMonths(6),
            'metadata' => json_encode([
                'document_type' => 'legacy_documentation',
                'system_name' => 'LegacyCRM v1.0',
                'decommission_date' => '2023-12-31',
                'replacement_system' => 'SalesForce Cloud',
                'archive_reason' => 'system_decommission',
                'data_retention_period' => '7 years',
                'access_restrictions' => ['confidential', 'historical_only'],
                'migration_notes' => 'Data migrated to new system, documentation preserved for compliance'
            ]),
            'file_path' => 'items/legacy_crm_docs.zip',
            'file_name' => 'legacy_crm_system_documentation_archive.zip',
            'file_size' => 12543000,
            'file_type' => 'application/zip',
            'slug' => Str::slug('Test Item - Archived Legacy System Documentation'),
            'created_at' => Carbon::now()->subYears(1),
            'updated_at' => Carbon::now()->subMonths(6),
        ]);
        $archivedItem->categories()->attach([$categories[1]->id, $categories[3]->id]);

        // ============================================================================
        // ITEM 8: MULTIMEDIA CONTENT - Different file types
        // ============================================================================
        $multimediaItem = Item::create([
            'title' => 'Test Item - Product Demo Video Tutorial',
            'description' => 'Screen recording video tutorial demonstrating the key features and functionality of our flagship product.',
            'collection_id' => $collection->id,
            'user_id' => $contentManager->id,
            'is_published' => true,
            'is_approved' => true,
            'is_archived' => false,
            'is_featured' => false,
            'approved_by' => $admin->id,
            'approved_at' => Carbon::now()->subDays(5),
            'metadata' => json_encode([
                'document_type' => 'video_tutorial',
                'video_duration' => '00:15:30',
                'resolution' => '1920x1080',
                'file_format' => 'MP4',
                'audio_track' => true,
                'subtitles_available' => true,
                'chapters' => [
                    '00:00:00' => 'Introduction',
                    '00:02:30' => 'Basic Setup',
                    '00:08:15' => 'Advanced Features',
                    '00:13:45' => 'Tips & Tricks'
                ],
                'production_team' => ['video_editor', 'script_writer', 'voice_artist'],
                'thumbnail_path' => 'thumbnails/product_demo_thumbnail.jpg'
            ]),
            'file_path' => 'items/product_demo.mp4',
            'file_name' => 'product_feature_demo_tutorial_hd.mp4',
            'file_size' => 25478000,
            'file_type' => 'video/mp4',
            'slug' => Str::slug('Test Item - Product Demo Video Tutorial'),
            'created_at' => Carbon::now()->subDays(8),
            'updated_at' => Carbon::now()->subDays(5),
        ]);
        $multimediaItem->categories()->attach([$categories[2]->id, $categories[3]->id, $categories[4]->id]);

        // ============================================================================
        // ITEM 9: SPREADSHEET DATA - Excel file
        // ============================================================================
        $spreadsheetItem = Item::create([
            'title' => 'Test Item - Financial Projections 2024-2026',
            'description' => 'Detailed financial projections and budget forecasts for the next three years with multiple scenarios and sensitivity analysis.',
            'collection_id' => $collection->id,
            'user_id' => $admin->id,
            'is_published' => false,
            'is_approved' => false,
            'is_archived' => false,
            'is_featured' => false,
            'approved_by' => null,
            'approved_at' => null,
            'metadata' => json_encode([
                'document_type' => 'financial_spreadsheet',
                'fiscal_years' => [2024, 2025, 2026],
                'scenarios' => ['base_case', 'optimistic', 'pessimistic'],
                'financial_metrics' => ['revenue', 'cogs', 'gross_margin', 'ebitda'],
                'assumptions' => [
                    'growth_rate' => '15% annually',
                    'inflation' => '2.5%',
                    'exchange_rate' => 'stable'
                ],
                'data_sources' => ['historical_data', 'market_research', 'industry_reports'],
                'formulas_used' => true,
                    'protected_cells' => true,
                'last_calculated' => Carbon::now()->subDays(2)->format('Y-m-d H:i:s')
            ]),
            'file_path' => 'items/financial_projections.xlsx',
            'file_name' => '3_year_financial_projections_2024_2026_master.xlsx',
            'file_size' => 3245000,
            'file_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'slug' => Str::slug('Test Item - Financial Projections 2024-2026'),
            'created_at' => Carbon::now()->subDays(6),
            'updated_at' => Carbon::now()->subDays(2),
        ]);
        $spreadsheetItem->categories()->attach([$categories[3]->id]);

        // ============================================================================
        // ITEM 10: IMAGE GALLERY - Multiple images
        // ============================================================================
        $imageItem = Item::create([
            'title' => 'Test Item - UI/UX Design Mockups Collection',
            'description' => 'Collection of high-fidelity UI/UX design mockups for the new mobile application interface across different devices and screen sizes.',
            'collection_id' => $collection->id,
            'user_id' => $contentManager->id,
            'is_published' => true,
            'is_approved' => true,
            'is_archived' => false,
            'is_featured' => true,
            'approved_by' => $admin->id,
            'approved_at' => Carbon::now()->subDays(4),
            'metadata' => json_encode([
                'document_type' => 'design_mockups',
                'design_tool' => 'Figma',
                'design_system' => 'Material Design 3',
                'devices_supported' => ['mobile', 'tablet', 'desktop'],
                'color_palette' => ['primary', 'secondary', 'accent', 'neutral'],
                'typography_scale' => ['h1', 'h2', 'h3', 'body1', 'body2'],
                'components_designed' => ['buttons', 'forms', 'navigation', 'cards', 'modals'],
                'designer' => 'Jane Smith',
                'design_version' => '2.3'
            ]),
            'file_path' => 'items/design_mockups.zip',
            'file_name' => 'mobile_app_ui_ux_mockups_collection_v2.3.zip',
            'file_size' => 45789000,
            'file_type' => 'application/zip',
            'slug' => Str::slug('Test Item - UI/UX Design Mockups Collection'),
            'created_at' => Carbon::now()->subDays(12),
            'updated_at' => Carbon::now()->subDays(4),
        ]);
        $imageItem->categories()->attach([$categories[2]->id, $categories[3]->id]);

        $this->command->info('Workflow test items created successfully!');
        $this->command->info('Total items created: ' . Item::count());
        $this->command->info('Items cover all workflow stages and data types');
    }
}