<?php

namespace admin\ratings;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class RatingServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Load routes, views, migrations from the package  
        $this->loadViewsFrom([
            base_path('Modules/Ratings/resources/views'), // Published module views first
            resource_path('views/admin/rating'), // Published views second
            __DIR__ . '/../resources/views'      // Package views as fallback
        ], 'rating');

        $this->mergeConfigFrom(__DIR__.'/../config/rating.php', 'rating.constants');
        
        // Also register module views with a specific namespace for explicit usage
        if (is_dir(base_path('Modules/Ratings/resources/views'))) {
            $this->loadViewsFrom(base_path('Modules/Ratings/resources/views'), 'ratings-module');
        }
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        // Also load migrations from published module if they exist
        if (is_dir(base_path('Modules/Ratings/database/migrations'))) {
            $this->loadMigrationsFrom(base_path('Modules/Ratings/database/migrations'));
        }
        $this->mergeConfigFrom(__DIR__ . '/../config/rating.php', 'rating.config');
        // Also merge config from published module if it exists
        if (file_exists(base_path('Modules/Ratings/config/ratings.php'))) {
            $this->mergeConfigFrom(base_path('Modules/Ratings/config/ratings.php'), 'rating.config');
        }
        
        // Only publish automatically during package installation, not on every request
        // Use 'php artisan ratings:publish' command for manual publishing
        // $this->publishWithNamespaceTransformation();
        
        // Standard publishing for non-PHP files
        $this->publishes([
            __DIR__ . '/../config/' => base_path('Modules/Ratings/config/'),
            __DIR__ . '/../database/migrations' => base_path('Modules/Ratings/database/migrations'),
            __DIR__ . '/../resources/views' => base_path('Modules/Ratings/resources/views/'),
        ], 'rating');
       
        $this->registerAdminRoutes();
    }

    protected function registerAdminRoutes()
    {
        if (!Schema::hasTable('admins')) {
            return; // Avoid errors before migration
        }

        $admin = DB::table('admins')
            ->orderBy('created_at', 'asc')
            ->first();
            
        $slug = $admin->website_slug ?? 'admin';

        Route::middleware('web')
            ->prefix("{$slug}/admin") // dynamic prefix
            ->group(function () {
                // Load routes from published module first, then fallback to package
                if (file_exists(base_path('Modules/Ratings/routes/web.php'))) {
                    $this->loadRoutesFrom(base_path('Modules/Ratings/routes/web.php'));
                } else {
                    $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
                }
            });
    }

    public function register()
    {
        // Register the publish command
        if ($this->app->runningInConsole()) {
            $this->commands([
                \admin\ratings\Console\Commands\PublishRatingsModuleCommand::class,
                \admin\ratings\Console\Commands\CheckModuleStatusCommand::class,
                \admin\ratings\Console\Commands\DebugRatingsCommand::class,
            ]);
        }
    }

    /**
     * Publish files with namespace transformation
     */
    protected function publishWithNamespaceTransformation()
    {
        // Define the files that need namespace transformation
        $filesWithNamespaces = [
            // Controllers
            __DIR__ . '/../src/Controllers/RatingManagerController.php' => base_path('Modules/Ratings/app/Http/Controllers/Admin/RatingManagerController.php'),
            
            // Models
            __DIR__ . '/../src/Models/Rating.php' => base_path('Modules/Ratings/app/Models/Rating.php'),
            
            // Routes
            __DIR__ . '/routes/web.php' => base_path('Modules/Ratings/routes/web.php'),
        ];

        foreach ($filesWithNamespaces as $source => $destination) {
            if (File::exists($source)) {
                // Create destination directory if it doesn't exist
                File::ensureDirectoryExists(dirname($destination));
                
                // Read the source file
                $content = File::get($source);
                
                // Transform namespaces based on file type
                $content = $this->transformNamespaces($content, $source);
                
                // Write the transformed content to destination
                File::put($destination, $content);
            }
        }
    }

    /**
     * Transform namespaces in PHP files
     */
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
            $content = $this->transformControllerNamespaces($content);
        } elseif (str_contains($sourceFile, 'Models')) {
            $content = $this->transformModelNamespaces($content);
        } elseif (str_contains($sourceFile, 'routes')) {
            $content = $this->transformRouteNamespaces($content);
        }

        return $content;
    }

    /**
     * Transform controller-specific namespaces
     */
    protected function transformControllerNamespaces($content)
    {
        // Update use statements for models and requests
        $content = str_replace(
            'use admin\\ratings\\Models\\Rating;',
            'use Modules\\Ratings\\app\\Models\\Rating;',
            $content
        );

        return $content;
    }

    /**
     * Transform model-specific namespaces
     */
    protected function transformModelNamespaces($content)
    {
        // Any model-specific transformations
        return $content;
    }

    /**
     * Transform request-specific namespaces
     */
    protected function transformRequestNamespaces($content)
    {
        // Any request-specific transformations
        return $content;
    }

    /**
     * Transform route-specific namespaces
     */
    protected function transformRouteNamespaces($content)
    {
        // Update controller references in routes
        $content = str_replace(
            'admin\\ratings\\Controllers\\RatingManagerController',
            'Modules\\Ratings\\app\\Http\\Controllers\\Admin\\RatingManagerController',
            $content
        );

        return $content;
    }
}
