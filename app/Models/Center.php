<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Maxxidev\Author\Traits\CaptureAuthors;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Center extends Model
{
    use HasFactory;
    use CaptureAuthors;
    use LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'state',
        'name',
        'code_name',
        'code_no',
        'contact',
        'webpage',
        'services',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'services' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Configure activity log options.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['state', 'name', 'code_name', 'code_no', 'contact', 'webpage', 'services', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn (string $eventName) => "Medical center {$eventName}")
            ->useLogName('center');
    }

    /**
     * Get active medical centers.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Filter by state.
     */
    public function scopeByState($query, $state)
    {
        return $query->where('state', $state);
    }

    /**
     * Filter by service type.
     */
    public function scopeByService($query, $service)
    {
        return $query->whereJsonContains('services', $service);
    }

    /**
     * Get all available states.
     */
    public static function getAvailableStates()
    {
        return self::distinct()->pluck('state')->sort()->values();
    }

    /**
     * Get all available service types.
     */
    public static function getAvailableServices()
    {
        $services = self::whereNotNull('services')
            ->get()
            ->pluck('services')
            ->flatten()
            ->unique()
            ->sort()
            ->values();

        return $services;
    }

    /**
     * Get formatted contact information.
     */
    public function getFormattedContactAttribute()
    {
        if (!$this->contact) {
            return null;
        }

        return nl2br(e($this->contact));
    }

    /**
     * Get service labels.
     */
    public function getServiceLabelsAttribute()
    {
        if (!$this->services) {
            return [];
        }

        $serviceLabels = \App\Enums\ServiceType::getLabels();

        return collect($this->services)->map(function ($service) use ($serviceLabels) {
            return $serviceLabels[$service] ?? $service;
        })->toArray();
    }
}
