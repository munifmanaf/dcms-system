<?php
namespace Database\Seeders;

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

        // Create Sample Items
        $items = [
            [
                'title' => 'Renewable Energy Solutions for Urban Areas',
                'description' => 'A comprehensive study of renewable energy integration in modern urban environments focusing on solar and wind power applications.',
                'user_id' => 1,
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
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'Machine Learning Applications in Structural Engineering',
                'description' => 'Exploring AI and ML techniques for structural analysis and design optimization in civil engineering projects.',
                'user_id' => 2,
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
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'Advanced Materials for Sustainable Construction',
                'description' => 'Research on novel construction materials with reduced environmental impact and enhanced durability.',
                'collection_id' => $researchPapers->id,
                'user_id' => 3,
                'metadata' => json_encode([
                    'dc_title' => ['Advanced Materials for Sustainable Construction'],
                    'dc_creator' => ['Prof. Robert Wilson', 'Lisa Zhang'],
                    'dc_subject' => ['Materials Science', 'Sustainable Construction', 'Green Building'],
                    'dc_description' => ['Research on novel construction materials with reduced environmental impact.'],
                    'dc_date_issued' => ['2024-04-10'],
                    'dc_type' => ['Research Paper'],
                    'dc_publisher' => ['Materials Research Journal'],
                    'dc_format' => ['PDF'],
                    'dc_identifier' => ['RP-2024-003']
                ]),
                'workflow_state' => 'published',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'Quantum Computing Applications in Chemistry',
                'description' => 'Applications of quantum computing in molecular modeling and chemical simulations for drug discovery.',
                'collection_id' => $journalArticles->id,
                'user_id' => 5,
                'metadata' => json_encode([
                    'dc_title' => ['Quantum Computing Applications in Chemistry'],
                    'dc_creator' => ['Dr. Amanda Lee', 'David Kim'],
                    'dc_subject' => ['Quantum Computing', 'Computational Chemistry', 'Quantum Algorithms'],
                    'dc_description' => ['Applications of quantum computing in molecular modeling and chemical simulations.'],
                    'dc_date_issued' => ['2024-03-22'],
                    'dc_type' => ['Journal Article'],
                    'dc_publisher' => ['Journal of Computational Chemistry'],
                    'dc_format' => ['PDF'],
                    'dc_identifier' => ['JA-2024-004']
                ]),
                'workflow_state' => 'published',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'Modernist Poetry in the Digital Age',
                'description' => 'Analysis of modernist poetry adaptations and interpretations in contemporary digital media.',
                'collection_id' => $literaryWorks->id,
                'user_id' => 5,
                'metadata' => json_encode([
                    'dc_title' => ['Modernist Poetry in the Digital Age'],
                    'dc_creator' => ['Dr. Elizabeth Wong'],
                    'dc_subject' => ['Modernist Poetry', 'Digital Humanities', 'Literary Analysis'],
                    'dc_description' => ['Analysis of modernist poetry adaptations in contemporary digital media.'],
                    'dc_date_issued' => ['2024-02-28'],
                    'dc_type' => ['Literary Analysis'],
                    'dc_publisher' => ['Arts Review Journal'],
                    'dc_format' => ['PDF'],
                    'dc_identifier' => ['LW-2024-005']
                ]),
                'workflow_state' => 'published',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'Climate Change Impact on Coastal Infrastructure',
                'description' => 'Analysis of sea-level rise effects on coastal structures and adaptation strategies for climate resilience.',
                'collection_id' => $mastersTheses->id,
                'user_id' => 5,
                'metadata' => json_encode([
                    'dc_title' => ['Climate Change Impact on Coastal Infrastructure'],
                    'dc_creator' => ['Maria Garcia'],
                    'dc_subject' => ['Climate Change', 'Coastal Engineering', 'Infrastructure'],
                    'dc_description' => ['Analysis of sea-level rise effects on coastal structures and adaptation strategies.'],
                    'dc_date_issued' => ['2024-02-18'],
                    'dc_type' => ['Thesis'],
                    'dc_publisher' => ['University Repository'],
                    'dc_format' => ['PDF'],
                    'dc_identifier' => ['ETD-2024-006']
                ]),
                'workflow_state' => 'draft',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'Artificial Intelligence in Creative Writing',
                'description' => 'Exploring the role of AI tools in assisting and enhancing creative writing processes.',
                'collection_id' => $literaryWorks->id,
                'user_id' => 4,
                'metadata' => json_encode([
                    'dc_title' => ['Artificial Intelligence in Creative Writing'],
                    'dc_creator' => ['Thomas Reed'],
                    'dc_subject' => ['Artificial Intelligence', 'Creative Writing', 'Digital Literature'],
                    'dc_description' => ['Exploring the role of AI tools in assisting creative writing processes.'],
                    'dc_date_issued' => ['2024-01-15'],
                    'dc_type' => ['Research Paper'],
                    'dc_publisher' => ['Digital Humanities Quarterly'],
                    'dc_format' => ['PDF'],
                    'dc_identifier' => ['LW-2024-007']
                ]),
                'workflow_state' => 'pending_review',
                'created_at' => now(),
                'updated_at' => now()
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
        $this->command->info('Published items: ' . Item::where('workflow_state', 'published')->count());
        $this->command->info('Draft items: ' . Item::where('workflow_state', 'draft')->count());
        $this->command->info('Pending review: ' . Item::where('workflow_state', 'pending_review')->count());
    }
}