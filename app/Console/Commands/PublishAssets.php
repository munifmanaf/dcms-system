<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class PublishAssets extends Command
{
    protected $signature = 'assets:publish';
    protected $description = 'Publish custom assets to public directory';

    public function handle()
    {
        // Ensure public/css directory exists
        if (!File::exists(public_path('css'))) {
            File::makeDirectory(public_path('css'), 0755, true);
        }

        // Copy CSS file
        $cssSource = resource_path('css/custom.css');
        $cssDestination = public_path('css/custom.css');
        
        if (File::exists($cssSource)) {
            File::copy($cssSource, $cssDestination);
            $this->info('Custom CSS published successfully!');
        } else {
            $this->error('Custom CSS source file not found!');
        }

        return 0;
    }
}
