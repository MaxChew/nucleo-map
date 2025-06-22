<?php

use Monolog\Logger;
use Illuminate\Support\Carbon;
use Monolog\Handler\StreamHandler;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use App\Models\UserLoginActivity;
use App\Enums\Status;

if (!function_exists('log_content')) {
    function log_content($file, $content, $additionals = [])
    {
        $logfile = storage_path("logs/{$file}");
        $logger = new Logger('CustomLogger');
        $logger->pushHandler(new StreamHandler($logfile));
        $logger->debug($content, $additionals);
    }
}

if (!function_exists('dblisten')) {
    function dblisten($log = null)
    {
        $logfile = null;
        if ($log) {
            $logfile = is_string($log) ? $log : 'query.log';
            File::delete(storage_path("logs/{$logfile}"));
        }
        DB::listen(function ($query) use ($logfile) {
            if ($logfile) {
                log_content($logfile, $query->sql, [$query->bindings, $query->time]);
            } else {
                dump($query->sql, $query->bindings, $query->time);
            }
        });
    }
}

if (!function_exists('start_case')) {
    function start_case($value)
    {
        return Str::title(Str::of(Str::studly($value), ' ')->snake());
    }
}

if (!function_exists('upper_case')) {
    function upper_case($value)
    {
        return strtoupper(start_case($value));
    }
}

if (!function_exists('lower_case')) {
    function lower_case($value)
    {
        return strtolower(start_case($value));
    }
}

if (!function_exists('set_intended')) {
    function set_intended($url)
    {
        session()->put('url.intended', $url);
    }
}

if (!function_exists('pr')) {
    function pr(...$expressions)
    {
        if ('cli' !== PHP_SAPI) {
            echo '<pre>';
        }

        Arr::map($expressions, 'print_r');

        if ('cli' !== PHP_SAPI) {
            echo '</pre>';
        }
    }
}

if (!function_exists('vd')) {
    function vd(...$expressions)
    {
        if ('cli' !== PHP_SAPI) {
            echo '<pre>';
        }

        var_dump(...$expressions);

        if ('cli' !== PHP_SAPI) {
            echo '</pre>';
        }
    }
}

if (!function_exists('prd')) {
    function prd(...$expressions)
    {
        pr(...$expressions);
        die(1);
    }
}

if (!function_exists('vdd')) {
    function vdd(...$expressions)
    {
        vd(...$expressions);
        die(1);
    }
}

if (!function_exists('carbon')) {
    function carbon($date, $tz = null)
    {
        return new Carbon($date, $tz);
    }
}

if (!function_exists('carbon_range')) {
        /**
     * Create a Carbon date range collection
     *
     * @param string|Carbon $from Start date
     * @param string|Carbon $to End date
     * @param ?DateInterval $interval Time interval (can be null)
     * @param ?string $tz Timezone (can be null) 
     * @return \Illuminate\Support\Collection
     */
    function carbon_range($from, $to, ?DateInterval $interval = null, ?string $tz = null)
    {
        $interval = $interval ?? new DateInterval('P1D');
        $current = $from = ($from instanceof Carbon) ? $from->copy() : carbon($from, $tz);
        $to = ($to instanceof Carbon) ? $to->copy() : carbon($to, $tz); 

        $range = collect();
        while ($current <= $to) {
            $range->push($current->copy());
            $current->add($interval);
        }
        return $range;
    }
}

if (!function_exists('format_date')) {
    function format_date($date, $format = 'j M Y')
    {
        return $date ? Illuminate\Support\Carbon::parse($date)->format($format) : null;
    }
}

if (!function_exists('format_datetime')) {
    function format_datetime($date, $format = 'j M Y H:i')
    {
        return $date ? Illuminate\Support\Carbon::parse($date)->format($format) : null;
    }
}

if (!function_exists('number_ordinal')) {
    function number_ordinal($num)
    {
        if (!in_array(($num % 100), [11, 12, 13])) {
            switch ($num % 10) {
                case 1:  return $num . 'st';
                case 2:  return $num . 'nd';
                case 3:  return $num . 'rd';
            }
        }
        return $num . 'th';
    }
}

if (!function_exists('dropdown_year')) {
    function dropdown_year($min, $max=null)
    {
        $array = [];
        for ($year = date('Y'); $year > date('Y') - $min; $year--) {
            $array[$year] = $year;
            //array_push($array, $year);
        }
        //$array = krsort($array);

        return $array;

    }
}

if (!function_exists('dropdown_hour_min')) {
    function dropdown_hour_min($military_time = false, $start_hour = 9, $end_hour = 8, $interval = 30)
    {
        $times = [];
        $current = new DateTime(sprintf('%02d:00', $start_hour));
        $end = new DateTime(sprintf('%02d:00', $end_hour));
        
                    // If end time is less than start time, it means crossing midnight, need to add 24 hours
        if ($end <= $current) {
            $end->modify('+1 day');
        }
        
        while ($current <= $end) {
            $format = $military_time ? 'H:i' : 'h:i A';
            $key = $current->format($format);
            $times[$key] = $key;
            
            $current->modify(sprintf('+%d minutes', $interval));
        }
        
        return $times;
    }
}

if (!function_exists('dropdown_hour')) {
    function dropdown_hour($military_time = false)
    {
        $array = [];
        $h = 0;

        if ($military_time) {
            while ($h < 24) {
                $key = $h;
                $value = date('H', strtotime(date('Y-m-d') . ' + ' . $h . ' hours'));
                $array[$key] = $value;
                $h++;
            }
        } else {
            while ($h < 12) {
                $key = $h + 1;
                $value = date('H', strtotime(date('Y-m-d') . ' + ' . $h + 1 . ' hours'));
                $array[$key] = $value;
                $h++;
            }
        }
        
        return $array;
    }
}

if (!function_exists('dropdown_min')) {
    function dropdown_min()
    {
        $array = [];
        $m = 0;

        while ($m < 60) {
            $key = date('i', strtotime(date('Y-m-d') . ' + ' . $m . ' mins'));
            $value = date('i', strtotime(date('Y-m-d') . ' + ' . $m . ' mins')) . ' mins';
            $array[$key] = $value;
            $m++;
        }

        array_multisort($array, SORT_DESC);

        return $array;
    }
}

if (!function_exists('uclower')) {
    function uclower($string)
    {
        return ucwords(strtolower($string));
    }
}

if (!function_exists('explode_filter')) {
    function explode_filter($seperator, $string)
    {
        return array_filter(Arr::map('trim', explode($seperator, $string)));
    }
}

if (!function_exists('class_uses_trait')) {
    function class_uses_trait($class, $trait)
    {
        return in_array($trait, class_uses_recursive($class));
    }
}

if (!function_exists('htmlattributes')) {
    function htmlattributes($attributes)
    {
        $html = [];
        foreach ((array)$attributes as $key => $value) {
            if (is_numeric($key)) {
                $key = $value;
            }

            if (!is_null($value)) {
                if ($key === 'class' && is_array($value)) {
                    $value = implode(' ', $value);
                } elseif ($key === 'style' && is_array($value)) {
                    $value = collect($value)->map(function ($val, $key) {
                        return is_null($val) ? null : $key . ':' . $val;
                    })->filter()->implode(';');
                }
                if (is_array($value) || is_object($value)) {
                    $value = json_encode($value);
                }
                $html[] = $key . '="' . htmlentities($value, ENT_QUOTES, 'UTF-8') . '"';
            }
        }
        return count($html) > 0 ? ' ' . implode(' ', $html) : '';
    }
}

if (!function_exists('currency')) {
    function currency($code = null, $exchangeRate = null)
    {

        return isset($code) ? App\Models\Currency::code($code, $exchangeRate) : App\Models\Currency::code(config('defaults.currency'));
    }
}

if (!function_exists('url_params')) {
    function url_params($url, $parameters)
    {
        if (is_array($parameters)) {
            $parameters = http_build_query($parameters);
        }
        if ($parameters) {
            $url .= (Str::contains($url, '?') ? '&' : '?') . $parameters;
        }
        return $url;
    }
}

if (!function_exists('consumer_url')) {
    function consumer_url($url, $parameters = [])
    {
        $url = config('defaults.consumer.url') . Str::start($url, '/');
        return url_params($url, $parameters);
    }
}

if (!function_exists('external_url')) {
    function external_url($base_uri, $url, $parameters = [])
    {
        $url = rtrim($base_uri, '/') . Str::of($url)->start('/');
        return url_params($url, $parameters);
    }
}

if (!function_exists('passport_url')) {
    function passport_url($url, $parameters = [])
    {
        return external_url(config('defaults.api.base_uri'), $url, $parameters);
    }
}

if (!function_exists('agent_url')) {
    function agent_url($url, $parameters = [])
    {
        $url = config('defaults.agent.url') . Str::start($url, '/');
        return url_params($url, $parameters);
    }
}

if (!function_exists('user_url')) {
    function user_url($url, $parameters = [])
    {
        $url = config('defaults.user.url') . Str::start($url, '/');
        return url_params($url, $parameters);
    }
}

if (!function_exists('object_to_array')) {
    function object_to_array($object)
    {   
        $arrays = [];

        foreach($object as $key => $value)
        {
            $arrays[strtolower($key)] = $value;
        }
        
        return $arrays;
    }
}

if (!function_exists('admin_breadcrumb')) {
    function admin_breadcrumb($class_title = null, $class_index_url = null, $method = null, $object_title = null, $extra = null)
    {
        $dashboardUrl = route(app('domain') . '.dashboard.index');

        $breadcrumb = [
            $dashboardUrl => '<i class="far fa-home"></i> Dashboard',
        ];

        if ($method == null) {
            $breadcrumb[$class_index_url] = $class_title;
            if ($extra) {
                $breadcrumb = array_merge($breadcrumb, $extra);
            }
            return $breadcrumb;
        }

        if ($method == 'create') {
            return array_merge($breadcrumb, [
                $class_index_url => $class_title,
                '#' => 'Add ' . Str::singular($class_title),
            ]);
        }

        if ($method == 'edit' || $method == 'show') {
            return array_merge($breadcrumb, [
                $class_index_url => $class_title,
                '#' => ($method === 'edit' ? 'Edit ' : 'View ') . ($object_title ?? 'Unknown'),
            ]);
        }

        return $breadcrumb;
    }
}

if (!function_exists('cleanSpecialChars')) {
    function cleanSpecialChars($string) {
        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
        $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
    
        return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
    }
}

if (!function_exists('format_number_in_k_notation')) {
    function format_number_in_k_notation(int $number): string
    {
        $suffixByNumber = function () use ($number) {
            if ($number < 1000) {
                return sprintf('%d', $number);
            }

            if ($number < 1000000) {
                return sprintf('%d%s', floor($number / 1000), 'K+');
            }

            if ($number >= 1000000 && $number < 1000000000) {
                return sprintf('%d%s', floor($number / 1000000), 'M+');
            }

            if ($number >= 1000000000 && $number < 1000000000000) {
                return sprintf('%d%s', floor($number / 1000000000), 'B+');
            }

            return sprintf('%d%s', floor($number / 1000000000000), 'T+');
        };

        return $suffixByNumber();
    }
}

if (!function_exists('unformat_number')) {
    function unformat_number($number) {
        return preg_replace('/[^\d.]/', '', $number);
    }
}

if (!function_exists('get_ip')) {
    function get_ip($changetoobject = null)
    {
        $ip = Request::ip();

        if ($ip == '127.0.0.1') {
            $ip = "202.187.25.59"; //default IP
        }

        $object = UserLoginActivity::where('last_ip', $ip)
                            ->where('curdate', date('Y-m-d'))
                            ->first();
        
        if ($object) {
            $default_lat = $object->lat ?? config('defaults.location.lat');
            $default_lng = $object->long ?? config('defaults.location.lng');
        } else {
            $data = null; //Location::get($ip);

            $default_lat = $data ? $data->latitude : config('defaults.location.lat');
            $default_lng = $data ? $data->longitude : config('defaults.location.lng');
        }

        if ($changetoobject) {
            return (object) [
                'ip' => $ip,
                'latitude' => $default_lat, //$data->latitude,
                'longitude' => $default_lng, //$data->longitude,
            ];
        } else {
            return [
                'ip' => $ip,
                'latitude' => $default_lat, //$data->latitude,
                'longitude' => $default_lng, //$data->longitude,
            ];
        }
    }

    if (!function_exists('extractLongLatFromGoogleMapsUrl')) {
        function extractLongLatFromGoogleMapsUrl($url)
        {
            $pattern = '/@([-0-9.]+),([-0-9.]+),/';
            preg_match($pattern, $url, $matches);
        
            if (count($matches) == 3) {
                $latitude = $matches[1];
                $longitude = $matches[2];
                return array(
                    "latitude" => $latitude, 
                    "longitude" => $longitude
                );
            } else {
                return null; // If the pattern doesn't match, return false or handle it as needed.
            }
        }
    }
}

if (!function_exists('hex_to_rgba')) {
    /**
     * Convert a HEX color to RGBA format.
     *
     * @param string $hex The HEX color code, e.g., '#FFF', '#000000'
     * @param float $alpha The alpha value for RGBA, ranges from 0 to 1
     * @return string The RGBA color string, e.g., 'rgba(255, 255, 255, 0.5)'
     */
    function hex_to_rgba($hex, $alpha = null)
    {   
         // Remove '#' if present
        $hex = ltrim($hex, '#');

        // If the hex color is shorthand (e.g., #FFF), convert to full form (e.g., #FFFFFF)
        if (strlen($hex) === 3) {
            $hex = str_repeat(substr($hex, 0, 1), 2) .
                   str_repeat(substr($hex, 1, 1), 2) .
                   str_repeat(substr($hex, 2, 1), 2);
        }
        
        // Convert hex to RGB values
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        if (empty($alpha)) {
            return "$r, $g, $b";
        } else {
            $alpha = 1;

            // Return RGBA color string
            return "rgba($r, $g, $b, $alpha)";
        }
    }

    if (!function_exists('getProgressByStatus')) {
        function getProgressByStatus($type = null, $status = null)
        {
           // Use static variables within the function to define constants and default steps
           static $TYPES = [
               'REPORT' => [
                   'steps' => [
                       '1' => [
                           'label' => 'Created',
                           'short_label' => 'New',
                       ], 
                       '2' => [
                           'label' => 'Waiting Tutor Submit', 
                           'short_label' => 'Pending',
                       ],
                       '3' => [
                           'label' => 'Waiting Client Update',
                           'short_label' => 'Review', 
                       ],
                       '4' => [
                           'label' => 'Completed',
                           'short_label' => 'Completed',
                       ]
                   ],
                   'status_step_map' => [
                       Status::NEW => '1',
                       Status::PENDING_REPORT => '2', 
                       Status::PENDING => '3'
                   ],
                   'completed_status_list' => Status::REPORT_COMPLETED_STATUS_LIST,
                   'status_map_key' => 'REPORT_STATUS_MAP'
               ],
               'COURSE' => [
                    'steps' => [
                        '1' => [
                            'label' => 'Created',
                            'short_label' => 'New',
                        ], 
                        '2' => [
                            'label' => 'Preparing Class', 
                            'short_label' => 'Preparing',
                        ],
                        '3' => [
                            'label' => 'Waiting Appproval',
                            'short_label' => 'Appproval', 
                        ],
                        '4' => [
                            'label' => 'Waiting Payment',
                            'short_label' => 'Payment', 
                        ],
                        '5' => [
                            'label' => 'Generating',
                            'short_label' => 'Waiting', 
                        ],
                        '6' => [
                            'label' => 'Completed',
                            'short_label' => 'Completed',
                        ]
                    ],
                    'status_step_map' => [
                        Status::NEW => '1',
                        Status::PENDING => '2', 
                        Status::PENDING_APPROVED => '3',
                        Status::PENDING_PAYMENT => '4',
                        Status::READY_GENERATING => '5',
                        Status::LIVE => '6',
                    ],
                    'completed_status_list' => Status::COURSE_COMPLETED_STATUS_LIST,
                    'status_map_key' => 'COURSE_STATUS_MAP'
                ],
               'LESSON' => [
                   'steps' => [
                       // Define course steps
                   ],
                   'status_step_map' => [
                       // Define status step mapping 
                   ],
                                           'completed_status_list' => [], // Completed status list
                   'status_map_key' => 'LESSON_STATUS_MAP'
               ],
           ];
        
                       // Initialize progress object
           $progress = [
               'steps' => [],
               'current_step' => 1
           ];
           
                       // If no type or invalid type, return empty progress directly
           if (!$type || !isset($TYPES[strtoupper($type)])) {
               return $progress;
           }
        
                       // Get type configuration
           $typeConfig = $TYPES[strtoupper($type)];
           
                       // Clone default steps
           $steps = $typeConfig['steps'];
        
                       // Process status
           if ($status) {
                               // Helper function to format labels
               $formatLabel = function($status) use ($typeConfig) {
                   return ucfirst(strtolower(
                       Status::getCustomValueByKey($typeConfig['status_map_key'], $status) ?? $status
                   ));
               };
        
                               // Update current step and label
               if (isset($typeConfig['status_step_map'][$status])) {
                   $progress['current_step'] = $typeConfig['status_step_map'][$status];
                   
                                       // Only update labels for specific statuses
                   if (in_array($status, [Status::PENDING_REPORT, Status::PENDING])) {
                       $steps[$progress['current_step']]['label'] = $formatLabel($status);
                   }
               }
                               // Handle completed status 
               elseif (!empty($typeConfig['completed_status_list']) && 
                       in_array($status, $typeConfig['completed_status_list'])) {
                   $lastStep = array_key_last($steps);
                   $progress['current_step'] = $lastStep;
                   $steps[$lastStep]['label'] = $formatLabel($status);
               }
           }
        
           $progress['steps'] = $steps;    
           return $progress;
        }
    } 
}

if (!function_exists('getStatusCounts')) {
    function getStatusCounts($query, $statusList, $notInStatusList = []) {
        $statusCounts = ['all' => $query->clone()->whereNotIn('status', $notInStatusList)->count()]; 
        
        // Remove statuses that should be excluded
        $filteredStatusList = array_diff($statusList, $notInStatusList);

        foreach ($filteredStatusList as $status) {
            $statusCounts[$status] = $query->clone()->where('status', $status)->count();
        }

        return $statusCounts;
    }
}

if (!function_exists('getUserActiveCounts')) {
    function getUserActiveCounts($query) {
        return [
            'all'      => $query->clone()->count(),
            'active'   => $query->clone()->where('is_active', true)->count(),
            'inactive' => $query->clone()->where('is_active', false)->count(),
        ];
    }
}

if (!function_exists('getYearsList')) {
    function getYearsList(string $model) {
        if (!class_exists($model) || !is_subclass_of($model, Model::class)) {
            throw new InvalidArgumentException("Invalid model: $model");
        }

        $years_ary = $model::select(DB::raw("YEAR(created_at) as year"))
            ->orderBy('year')
            ->groupBy('year')
            ->pluck('year');

        return [
            'years_ary' => $years_ary,
            'years' => $years_ary->mapWithKeys(fn($year) => [$year => $year])
        ];
    }
}