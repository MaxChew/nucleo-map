<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CenterService;

class MapController extends Controller
{
    protected $centerService;

    public function __construct(CenterService $centerService)
    {
        $this->centerService = $centerService;
    }

    /**
     * Display map page
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Get basic statistics
        $summary = $this->centerService->getSummary();
        $filterOptions = $this->centerService->getFilterOptions();
        
        // SEO and page configuration
        $pageConfig = [
            'title' => 'Nucleo Map - Malaysia Medical Centers Map',
            'description' => 'Explore nuclear medicine centers across Malaysia',
            'keywords' => 'nuclear medicine, Malaysia, medical centers, PET, SPECT, radioactive iodine',
            'og_title' => 'Nucleo Map - Malaysia Medical Centers Map',
            'og_description' => 'Search and locate nuclear medicine medical centers in Malaysian states, providing complete service information and contact details.',
            'og_image' => asset('favicon-64x64.png'),
            'og_url' => $request->url(),
        ];

        // Map configuration
        $mapConfig = [
            'center' => ['lat' => 4.2105, 'lng' => 101.9758], // Malaysia center point
            'zoom' => 7,
            'api_base_url' => url('/api/public'),
            'total_centers' => $summary->total ?? 0,
            'total_states' => count($filterOptions['states']),
            'total_services' => count($filterOptions['services']),
        ];

        // Initial filter parameters
        $initialFilters = [
            'keyword' => $request->get('keyword', ''),
            'state' => $request->get('state', 'all'),
            'service' => $request->get('service', 'all'),
            'embed' => false
        ];

        return view('map.index', compact(
            'pageConfig',
            'mapConfig', 
            'initialFilters',
            'summary',
            'filterOptions'
        ));
    }

    /**
     * Get embedded map (for WordPress iframe)
     *
     * @return \Illuminate\View\View
     */
    public function embed(Request $request)
    {
        // Get basic statistics
        $summary = $this->centerService->getSummary();
        $filterOptions = $this->centerService->getFilterOptions();
        
        // Embedded configuration (simplified version)
        $mapConfig = [
            'center' => ['lat' => 4.2105, 'lng' => 101.9758],
            'zoom' => 7,
            'api_base_url' => url('/api/public'),
            'embed_mode' => true,
        ];

        // Initial filter parameters
        $initialFilters = [
            'keyword' => $request->get('keyword', ''),
            'state' => $request->get('state', 'all'),
            'service' => $request->get('service', 'all'),
            'embed' => true
        ];

        // Create view response and set iframe-friendly headers
        $response = response()->view('map.embed', compact(
            'mapConfig', 
            'initialFilters',
            'summary',
            'filterOptions'
        ));
        
        $response->header('X-Frame-Options', 'ALLOWALL');
        $response->header('Content-Security-Policy', 'frame-ancestors *');
        
        return $response;
    }
} 