<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class Temp extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        if ($this->resource instanceof Collection) {
            return $this->resource->toArray();
        }

        return array_merge(
            $this->resource->toArray(),
            [
                'created_at' => ($this->created_at) ? optional($this->created_at)->format('d-m-Y') : null,
                'updated_at' => ($this->updated_at) ? optional($this->updated_at)->format('d-m-Y h:ia') : null,
                'created_by' => new User($this->whenLoaded('createdBy')),
                'updated_by' => new User($this->whenLoaded('updatedBy')),
            ]
        );
    }
}