<?php

namespace App\Services;

use App\Models\Center;
use Illuminate\Support\Facades\DB;

class CenterService
{
    /**
     * Get statistical summary data
     */
    public function getSummary()
    {
        $total = Center::count();
        $active = Center::where('is_active', true)->count();
        $inactive = Center::where('is_active', false)->count();
        
        return (object) [
            'all' => $total,
            'active' => $active,
            'inactive' => $inactive,
            'total' => $total,
            'by_state' => Center::select('state', DB::raw('count(*) as count'))
                ->groupBy('state')
                ->orderBy('state')
                ->get()
                ->pluck('count', 'state')
                ->toArray(),
            'by_service' => $this->getServiceStatistics(),
        ];
    }

    /**
     * Get service statistics
     */
    public function getServiceStatistics()
    {
        $centers = Center::whereNotNull('services')->get();
        $serviceStats = [];
        
        foreach ($centers as $center) {
            if ($center->services) {
                foreach ($center->services as $service) {
                    $serviceStats[$service] = ($serviceStats[$service] ?? 0) + 1;
                }
            }
        }
        
        arsort($serviceStats);
        return $serviceStats;
    }

    /**
     * Check if medical center can be deleted
     */
    public function canDelete(Center $center)
    {
        // Check if there is associated data
        // More checks can be added as needed
        return true;
    }

    /**
     * Batch import medical center data
     */
    public function importFromJson(array $centersData)
    {
        DB::transaction(function () use ($centersData) {
            foreach ($centersData as $centerData) {
                Center::updateOrCreate(
                    ['code_name' => $centerData['code_name']],
                    [
                        'state' => $centerData['state'],
                        'name' => $centerData['name'],
                        'code_no' => $centerData['code_no'],
                        'contact' => $centerData['contact'],
                        'webpage' => $centerData['webpage'],
                        'services' => $centerData['services'] ?? [],
                        'is_active' => true,
                    ]
                );
            }
        });
    }

    /**
     * Get filter options
     */
    public function getFilterOptions()
    {
        return [
            'states' => Center::getAvailableStates(),
            'services' => Center::getAvailableServices(),
        ];
    }

    /**
     * Get map data - includes coordinate information
     */
    public function getMapData()
    {
        $centers = Center::active()
            ->select('id', 'state', 'name', 'code_name', 'code_no', 'contact', 'webpage', 'services')
            ->get();

        // State coordinate mapping
        $stateCoordinates = $this->getStateCoordinates();
        
        $mapData = [];
        foreach ($centers as $center) {
            $state = $center->state;
            
            if (isset($stateCoordinates[$state])) {
                $mapData[] = [
                    'id' => $center->id,
                    'name' => $center->name,
                    'code_name' => $center->code_name,
                    'code_no' => $center->code_no,
                    'state' => $state,
                    'contact' => $center->contact,
                    'webpage' => $center->webpage,
                    'services' => $center->services ?? [],
                    'service_labels' => $center->service_labels,
                    'coordinates' => $stateCoordinates[$state],
                ];
            }
        }
        
        return [
            'centers' => $mapData,
            'states' => array_keys($stateCoordinates),
            'services' => $this->getUniqueServices(),
            'total_count' => count($mapData),
        ];
    }

    /**
     * Get state coordinate mapping
     */
    public function getStateCoordinates()
    {
        return [
            "Pulau Pinang" => ["lat" => 5.4141, "lng" => 100.3288],
            "Perak" => ["lat" => 4.5975, "lng" => 101.0901],
            "Selangor" => ["lat" => 3.0738, "lng" => 101.5183],
            "Melaka" => ["lat" => 2.1896, "lng" => 102.2501],
            "Johor" => ["lat" => 1.4927, "lng" => 103.7414],
            "Pahang" => ["lat" => 3.8077, "lng" => 103.3260],
            "Kelantan" => ["lat" => 6.1254, "lng" => 102.2386],
            "Terengganu" => ["lat" => 5.3117, "lng" => 103.1324],
            "Kedah" => ["lat" => 6.1248, "lng" => 100.3678],
            "Perlis" => ["lat" => 6.4449, "lng" => 100.2048],
            "Negeri Sembilan" => ["lat" => 2.7297, "lng" => 101.9381],
            "Sabah" => ["lat" => 5.9788, "lng" => 116.0753],
            "Sarawak" => ["lat" => 1.5533, "lng" => 110.3592],
            "W.P. Kuala Lumpur" => ["lat" => 3.1390, "lng" => 101.6869],
            "W.P. Putrajaya" => ["lat" => 2.9264, "lng" => 101.6964],
            "W.P. Labuan" => ["lat" => 5.2831, "lng" => 115.2308],
        ];
    }

    /**
     * Get all unique service types
     */
    public function getUniqueServices()
    {
        return Center::getAvailableServices();
    }
} 