<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeFeature extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:make-feature';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate CRUD structure (Controller, Service, Repository, Request)';

    protected Filesystem $files;


    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }


    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = Str::studly($this->argument('name'));

        $this->makeDirectories();
        // $this->makeRepository($name);
        // $this->makeService($name);
        // $this->makeController($name);
        // $this->makeRequest($name);

        $this->info("CRUD {$name} generated successfully");
    }

    protected function makeDirectories()
    {
        $dirs = [
            app_path('Repositories'),
            app_path('Services'),
            app_path('Interfaces/Repositories'),
            app_path('Http/Controllers'),
            app_path('Http/Requests'),
        ];

        foreach ($dirs as $dir) {
            if (! $this->files->exists($dir)) {
                $this->files->makeDirectory($dir, 0755, true);
            }
        }
    }
}
