<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class Center extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'state' => $this->state,
            'name' => $this->name,
            'code_name' => $this->code_name,
            'code_no' => $this->code_no,
            'contact' => $this->contact,
            'webpage' => $this->webpage,
            'services' => $this->services ?? [],
            'service_labels' => $this->service_labels,
            'formatted_contact' => $this->formatted_contact,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by ?? null,
            'updated_by' => $this->updated_by ?? null,
            
            // 关联数据
            'creator' => $this->whenLoaded('creator', function () {
                return [
                    'id' => $this->creator->id,
                    'name' => $this->creator->name,
                ];
            }),
            'updater' => $this->whenLoaded('updater', function () {
                return [
                    'id' => $this->updater->id,
                    'name' => $this->updater->name,
                ];
            }),
            
            // 格式化时间
            'created_at_formatted' => $this->formatDateTime($this->created_at),
            'updated_at_formatted' => $this->formatDateTime($this->updated_at),
            'created_at_with_time' => $this->formatDateTime($this->created_at, true),
            'updated_at_with_time' => $this->formatDateTime($this->updated_at, true),
        ];
    }
    
    /**
     * 格式化日期时间
     */
    protected function formatDateTime(?Carbon $datetime, bool $includeTime = false): ?string
    {
        if (!$datetime) {
            return null;
        }
        
        $format = 'd-m-Y';
        if ($includeTime) {
            $format .= ' h:ia';
        }
        
        return $datetime->format($format);
    }
} 