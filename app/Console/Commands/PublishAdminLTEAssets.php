<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class PublishAdminLTEAssets extends Command
{
    protected $signature = 'adminlte:publish';
    protected $description = 'Publish AdminLTE assets to public directory';

    public function handle()
    {
        // Create directories if they don't exist
        $directories = [
            public_path('vendor'),
            public_path('vendor/adminlte'),
            public_path('vendor/fontawesome-free'),
            public_path('vendor/jquery'),
            public_path('vendor/bootstrap'),
        ];

        foreach ($directories as $directory) {
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
            }
        }

        // Copy AdminLTE assets
        File::copyDirectory(base_path('node_modules/admin-lte/dist'), public_path('vendor/adminlte/dist'));
        File::copyDirectory(base_path('node_modules/@fortawesome/fontawesome-free'), public_path('vendor/fontawesome-free'));
        
        // Copy jQuery
        File::copy(
            base_path('node_modules/admin-lte/plugins/jquery/jquery.min.js'),
            public_path('vendor/jquery/jquery.min.js')
        );
        
        // Copy Bootstrap
        File::copyDirectory(base_path('node_modules/admin-lte/plugins/bootstrap'), public_path('vendor/bootstrap'));

        $this->info('AdminLTE assets published successfully!');
    }
}