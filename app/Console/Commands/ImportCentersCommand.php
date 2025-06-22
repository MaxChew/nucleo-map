<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CenterService;
use Illuminate\Support\Facades\File;

class ImportCentersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'centers:import {--file=centers.json : JSON file path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import medical center data from JSON file';

    protected $centerService;

    public function __construct(CenterService $centerService)
    {
        parent::__construct();
        $this->centerService = $centerService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filePath = base_path($this->option('file'));
        
        if (!File::exists($filePath)) {
            $this->error("File does not exist: {$filePath}");
            return 1;
        }
        
        $this->info("Importing medical center data from {$filePath}...");
        
        try {
            $jsonContent = File::get($filePath);
            $centersData = json_decode($jsonContent, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->error('JSON file format error: ' . json_last_error_msg());
                return 1;
            }
            
            if (!is_array($centersData)) {
                $this->error('JSON file content must be in array format');
                return 1;
            }
            
            $this->info("Found " . count($centersData) . " medical center records");
            
            $progressBar = $this->output->createProgressBar(count($centersData));
            $progressBar->start();
            
            $this->centerService->importFromJson($centersData);
            
            $progressBar->finish();
            $this->newLine();
            
            $this->info('✅ Medical center data imported successfully!');
            
            // Display statistics
            $summary = $this->centerService->getSummary();
            $this->table(
                ['Statistics', 'Count'],
                [
                    ['Total', $summary['total']],
                    ['Active', $summary['active']],
                    ['Inactive', $summary['inactive']],
                ]
            );
            
            // Display statistics grouped by state
            if (!empty($summary['by_state'])) {
                $this->newLine();
                $this->info('📊 Statistics by state:');
                $stateRows = [];
                foreach ($summary['by_state'] as $state => $count) {
                    $stateRows[] = [$state, $count];
                }
                $this->table(['State', 'Count'], $stateRows);
            }
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error('Import failed: ' . $e->getMessage());
            return 1;
        }
    }
} 