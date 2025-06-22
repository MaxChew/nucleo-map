<?php

namespace Database\Seeders;

use App\Models\Center;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class CenterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Importing medical center data...');

        $jsonPath = base_path('centers.json');

        if (!File::exists($jsonPath)) {
            $this->command->error('centers.json file does not exist');

            return;
        }

        $jsonContent = File::get($jsonPath);
        $centersData = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->command->error('JSON file format error: '.json_last_error_msg());

            return;
        }

        // Clear existing data
        Center::truncate();

        $progressBar = $this->command->getOutput()->createProgressBar(count($centersData));
        $progressBar->start();

        foreach ($centersData as $centerData) {
            Center::create([
                'state' => $centerData['state'],
                'name' => $centerData['name'],
                'code_name' => $centerData['code_name'],
                'code_no' => $centerData['code_no'],
                'contact' => $centerData['contact'],
                'webpage' => $centerData['webpage'],
                'services' => $centerData['services'] ?? [],
                'is_active' => true,
            ]);

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->command->newLine();

        $this->command->info('✅ Successfully imported '.count($centersData).' medical centers');

        // Display statistics
        $total = Center::count();
        $byState = Center::select('state')
            ->selectRaw('count(*) as count')
            ->groupBy('state')
            ->orderBy('state')
            ->get();

        $this->command->table(
            ['State', 'Count'],
            $byState->map(function ($item) {
                return [$item->state, $item->count];
            })->toArray()
        );
    }
}
