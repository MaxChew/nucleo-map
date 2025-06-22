<?php

namespace Maxxidev\Support;

use Api;
use Auth;
use Illuminate\Support\Carbon;
use Illuminate\Cache\CacheManager;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class DefaultManager
{
    protected $cache;
    protected $configs;
    protected $cacheMinutes = 1440;

    public function __construct(CacheManager $cache, array $configs = [])
    {
        $this->cache = $cache->tags('defaults');
        $this->configs = $configs;
    }

    public function get($key, $default = null)
    {
        return Arr::get($this->configs, $key, $default);
    }

    public function flush()
    {
        return $this->cache->flush();
    }

    public function country()
    {
        return $this->cache->remember('defaults.country', $this->cacheMinutes, function () {
            return Api::get(passport_url('shared/public/countries/' . $this->get('country')))->json()->data;
        });
    }

    public function state()
    {
        return $this->cache->tags('areas')->remember('defaults.state', $this->cacheMinutes, function () {
            return Api::get(passport_url('shared/public/countries/' . $this->get('country') . '/states/' . $this->get('state')))->json()->data;
        });
    }

    public function countryList()
    {
        return collect($this->cache->tags('countries')->remember('defaults.countryList', $this->cacheMinutes, function () {
            return Api::get(passport_url('shared/public/countries'))->json()->data;
        }));
    }

    public function stateList($country = null, $withlistingcount = null, $categories = null, $type = null)
    {
        $country = $country ?? $this->get('country');

        if ($withlistingcount) {
            if ($categories) {
                return Api::get(passport_url("shared/public/countries/{$country}/states/listings", ['categories' => $categories]))->json()->data;
            } else if ($type) {
                return Api::get(passport_url("shared/public/countries/{$country}/states/listings", ['type' => $type]))->json()->data;
            } else {
                return Api::get(passport_url("shared/public/countries/{$country}/states/listings"))->json()->data;
            }
        } else {
            return $this->cache->tags('areas')->remember("defaults.stateList.{$country}", $this->cacheMinutes, function () use ($country) {
                return Api::get(passport_url("shared/public/countries/{$country}/states"))->json()->data;
            });
        }
    }

    public function cityList($country = null, $state = null, $withlistingcount = null, $categories = null, $type = null)
    {
        $country = $country ?? $this->get('country');
        $state = $state ?? $this->get('state');

        if ($withlistingcount) {
            //return $this->cache->tags('citys')->remember("defaults.cityList.listings.{$country}", $this->cacheMinutes, function () use ($country, $state) {
                if ($categories) {
                    return Api::get(passport_url("shared/public/countries/{$country}/states/{$state}/citys/listings", ['categories' => $categories]))->json()->data;
                } else if ($type) {
                    return Api::get(passport_url("shared/public/countries/{$country}/states/{$state}/citys/listings", ['type' => $type]))->json()->data;
                } else {
                    return Api::get(passport_url("shared/public/countries/{$country}/states/{$state}/citys/listings"))->json()->data;
                }
            //});  
        } else {
            return Api::get(passport_url("shared/public/countries/{$country}/states/{$state}/citys"))->json()->data;
        }
    }

    public function currencyList()
    {
        return collect($this->cache->tags('currencies')->remember("defaults.currencyList", $this->cacheMinutes, function () {
            return Api::get(passport_url("shared/public/currencies?consumer=1"))->json()->data;
        }));
    }

    public function latestNotifications()
    {
        if ($id = Auth::id()) {
            return $this->cache->tags(['notifications', "notifications:user:{$id}"])->remember("defaults.latestNotifications.{$id}", $this->cacheMinutes, function () {
                return Api::get(passport_url('shared/private/notifications', ['with_unread' => 1, 'page'=> 1, 'per_page' => 5]))
                    ->json();
            });
        }
    }

    public function timezoneList()
    {
        return $this->cache->remember('defaults.timezoneList', $this->cacheMinutes, function () {
            return collect(\DateTimeZone::listIdentifiers())
                ->reduce(function ($timezones, $timezone) {
                    $hour = Carbon::now($timezone)->format('P');
                    $city = str_replace('_', ' ', Str::after($timezone, '/'));
                    $timezones[$timezone] = "$city (UTC $hour)";
                    return $timezones;
                }, collect())->sort();
        });
    }

    public function languageList()
    {
        return config('localization.locales', []);
    }

    public function walletSummary()
    {
        if (Auth::user()) {
            $user = Auth::user();
            //return $this->cache->tags('wallet')->remember("wallet.consumer.{$user->id}", $this->cacheMinutes, function () use ($user) {
                return Api::get(passport_url('shared/user/private/transactions/summary'))->json()->data;
            //});
        }
    }

    /**
     * Get student list
     * 
     * @param string|null $search Search keywords
     * @param string|null $educationLevel Education level
     * @param bool $useCache Whether to use cache
     * @return array Student list data
     */
    public function studentList($search = null, $educationLevel = null, $useCache = true)
    {
        $params = [];
        
        if ($search) {
            $params['search'] = $search;
        }
        
        if ($educationLevel) {
            $params['education_level'] = $educationLevel;
        }
        
        $cacheKey = "defaults.studentList." . md5(json_encode($params));
        
        if ($useCache) {
            return $this->cache->tags('students')->remember($cacheKey, $this->cacheMinutes, function () use ($params) {
                return Api::get(passport_url('shared/public/students', $params))->json()->data;
            });
        } else {
            return Api::get(passport_url('shared/public/students', $params))->json()->data;
        }
    }

    public function categoriesList($type = null, $map = null)
    {
        if ($type) {
            return $this->cache->remember("defaults.Categories.{$type}", $this->cacheMinutes, function () use ($type) {
                return Api::get(passport_url("shared/public/categories/{$type}"))->json()->data;
            });
        } else {
           
            if ($map) {
                $categories = Api::get(route('api.shared.categories.index.map'))->json()->data;

                return $categories;

            } else {
                return $this->cache->tags('defaults')->remember('categories', $this->cacheMinutes, function () {
                    return Api::get(route('api.shared.categories.index'))->json()->data;
                });
            //return collect($categories)->groupBy('type');
            //});

            }
        }
    }

    

    public function petsList($map = null)
    {
        if ($map) {
            return Api::get(route('api.shared.hashtags.pets.index'))->json()->data;

        } else {
            return $this->cache->tags('defaults')->remember('pets', $this->cacheMinutes, function () {
                return Api::get(route('api.shared.hashtags.pets.index'))->json()->data;
            });
        }
    }

    public function HashTagTopView($limit = null) {
        if ($limit == null) {
            $limit = 8;
        }

        return $this->cache->tags('defaults')->remember('hashtag.topview', $this->cacheMinutes, function () use ($limit){
            return Api::get(passport_url('shared/public/hashtags/topview/' . $limit))->json()->data;
        });
        
    }

    /**
     * Get supplier list
     * 
     * @param string|null $search Search keywords
     * @param bool $useCache Whether to use cache
     * @return array Supplier list data
     */
    public function supplierList($search = null, $useCache = true)
    {
        $params = [];
        
        if ($search) {
            $params['search'] = $search;
        }
        
        $cacheKey = "defaults.supplierList." . md5(json_encode($params));
        
        if ($useCache) {
            return $this->cache->tags('suppliers')->remember($cacheKey, $this->cacheMinutes, function () use ($params) {
                return Api::get(passport_url('shared/public/suppliers', $params))->json()->data;
            });
        } else {
            return Api::get(passport_url('shared/public/suppliers', $params))->json()->data;
        }
    }

    /**
     * Get tutor list
     * 
     * @param string|null $search Search keywords
     * @param string|null $location Location filter
     * @param bool $ableOnline Whether online teaching is supported
     * @param bool $useCache Whether to use cache
     * @return array Tutor list data
     */
    public function tutorList($search = null, $location = null, $ableOnline = null, $useCache = true)
    {
        $params = [];
        
        if ($search) {
            $params['search'] = $search;
        }
        
        if ($location) {
            $params['location'] = $location;
        }
        
        if ($ableOnline !== null) {
            $params['able_online'] = $ableOnline;
        }
        
        $cacheKey = "defaults.tutorList." . md5(json_encode($params));
        
        if ($useCache) {
            return $this->cache->tags('tutors')->remember($cacheKey, $this->cacheMinutes, function () use ($params) {
                return Api::get(passport_url('shared/public/tutors', $params))->json()->data;
            });
        } else {
            return Api::get(passport_url('shared/public/tutors', $params))->json()->data;
        }
    }

    /**
     * Get subject list
     * 
     * @param string|null $search Search keywords
     * @param string|null $educationLevel Education level
     * @param bool|null $isCore Whether it's a core subject
     * @param bool $useCache Whether to use cache
     * @return array Subject list data
     */
    public function subjectList($search = null, $educationLevel = null, $isCore = null, $useCache = true)
    {
        $params = [];
        
        if ($search) {
            $params['search'] = $search;
        }
        
        if ($educationLevel) {
            $params['education_level'] = $educationLevel;
        }
        
        if ($isCore !== null) {
            $params['is_core'] = $isCore;
        }
        
        $cacheKey = "defaults.subjectList." . md5(json_encode($params));
        
        if ($useCache) {
            return $this->cache->tags('subjects')->remember($cacheKey, $this->cacheMinutes, function () use ($params) {
                return Api::get(passport_url('shared/public/subjects', $params))->json()->data;
            });
        } else {
            return Api::get(passport_url('shared/public/subjects', $params))->json()->data;
        }
    }

    /**
     * Get client list
     * 
     * @param string|null $search Search keywords
     * @param string|null $role Role filter
     * @param bool $useCache Whether to use cache
     * @return array Client list data
     */
    public function clientList($search = null, $role = null, $useCache = true)
    {
        $params = [];
        
        if ($search) {
            $params['search'] = $search;
        }
        
        if ($role) {
            $params['role'] = $role;
        }
        
        $cacheKey = "defaults.clientList." . md5(json_encode($params));
        
        if ($useCache) {
            return $this->cache->tags('clients')->remember($cacheKey, $this->cacheMinutes, function () use ($params) {
                return Api::get(passport_url('shared/public/clients', $params))->json()->data;
            });
        } else {
            return Api::get(passport_url('shared/public/clients', $params))->json()->data;
        }
    }
}
