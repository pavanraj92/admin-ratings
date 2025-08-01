<?php

namespace admin\ratings\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class PublishRatingsModuleCommand extends Command
{
    protected $signature = 'ratings:publish {--force : Force overwrite existing files}';
    protected $description = 'Publish Ratings module files with proper namespace transformation';

    public function handle()
    {
        $this->info('Publishing Ratings module files...');

        // Check if module directory exists
        $moduleDir = base_path('Modules/Ratings');
        if (!File::exists($moduleDir)) {
            File::makeDirectory($moduleDir, 0755, true);
        }

        // Publish with namespace transformation
        $this->publishWithNamespaceTransformation();
        
        // Publish other files
        $this->call('vendor:publish', [
            '--tag' => 'rating',
            '--force' => $this->option('force')
        ]);

        // Update composer autoload
        $this->updateComposerAutoload();

        $this->info('Ratings module published successfully!');
        $this->info('Please run: composer dump-autoload');
    }

    protected function publishWithNamespaceTransformation()
    {
        $basePath = dirname(dirname(__DIR__)); // Go up to packages/admin/ratings/src
        
        $filesWithNamespaces = [
            // Controllers
            $basePath . '/Controllers/RatingManagerController.php' => base_path('Modules/Ratings/app/Http/Controllers/Admin/RatingManagerController.php'),
            
            // Models
            $basePath . '/Models/Rating.php' => base_path('Modules/Ratings/app/Models/Rating.php'),
            
            // Routes
            $basePath . '/routes/web.php' => base_path('Modules/Ratings/routes/web.php'),
        ];

        foreach ($filesWithNamespaces as $source => $destination) {
            if (File::exists($source)) {
                File::ensureDirectoryExists(dirname($destination));
                
                $content = File::get($source);
                $content = $this->transformNamespaces($content, $source);
                
                File::put($destination, $content);
                $this->info("Published: " . basename($destination));
            } else {
                $this->warn("Source file not found: " . $source);
            }
        }
    }

    protected function transformNamespaces($content, $sourceFile)
    {
        // Define namespace mappings
        $namespaceTransforms = [
            // Main namespace transformations
            'namespace admin\\ratings\\Controllers;' => 'namespace Modules\\Ratings\\app\\Http\\Controllers\\Admin;',
            'namespace admin\\ratings\\Models;' => 'namespace Modules\\Ratings\\app\\Models;',
            
            // Use statements transformations
            'use admin\\ratings\\Controllers\\' => 'use Modules\\Ratings\\app\\Http\\Controllers\\Admin\\',
            'use admin\\ratings\\Models\\' => 'use Modules\\Ratings\\app\\Models\\',
            
            // Class references in routes
            'admin\\ratings\\Controllers\\RatingManagerController' => 'Modules\\Ratings\\app\\Http\\Controllers\\Admin\\RatingManagerController',
        ];

        // Apply transformations
        foreach ($namespaceTransforms as $search => $replace) {
            $content = str_replace($search, $replace, $content);
        }

        // Handle specific file types
        if (str_contains($sourceFile, 'Controllers')) {
            $content = str_replace('use admin\\ratings\\Models\\Rating;', 'use Modules\\Ratings\\app\\Models\\Rating;', $content);
        }

        return $content;
    }

    protected function updateComposerAutoload()
    {
        $composerFile = base_path('composer.json');
        $composer = json_decode(File::get($composerFile), true);

        // Add module namespace to autoload
        if (!isset($composer['autoload']['psr-4']['Modules\\Ratings\\'])) {
            $composer['autoload']['psr-4']['Modules\\Ratings\\'] = 'Modules/Ratings/app/';
            
            File::put($composerFile, json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            $this->info('Updated composer.json autoload');
        }
    }
}
