<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Role extends JsonResource
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
            'name' => $this->name,
            'display_name' => $this->display_name ?? ucfirst($this->name),
            'description' => $this->description,
            'guard_name' => $this->guard_name,
            'permissions' => $this->whenLoaded('permissions', function () {
                return $this->permissions->map(function ($permission) {
                    return [
                        'id' => $permission->id,
                        'name' => $permission->name,
                    ];
                });
            }),
            'users' => $this->whenLoaded('users', function () {
                return $this->users->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                    ];
                });
            }),
            'permissions_count' => $this->whenLoaded('permissions', function () {
                return $this->permissions->count();
            }),
            'users_count' => $this->whenLoaded('users', function () {
                return $this->users->count();
            }),
            'can_delete' => $this->when(
                $this->relationLoaded('users'),
                function () {
                    return !in_array($this->name, ['super-admin', 'admin']) && $this->users->count() == 0;
                }
            ),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by ?? null,
            'updated_by' => $this->updated_by ?? null,
        ];
    }
} 