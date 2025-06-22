<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class User extends JsonResource
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
            'email' => $this->email,
            'mobile' => $this->mobile,
            'gender' => $this->gender,
            'is_active' => $this->is_active,
            'email_verified_at' => $this->email_verified_at,
            'roles' => $this->whenLoaded('roles', function () {
                return $this->roles->pluck('name');
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by ?? null,
            'updated_by' => $this->updated_by ?? null,
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
    
    /**
     * 移除敏感数据
     */
    protected function removeSensitiveData(array &$data): void
    {
        $sensitiveFields = [
            'password',
            'remember_token',
            'deleted_at',
        ];
        
        foreach ($sensitiveFields as $field) {
            unset($data[$field]);
        }
    }
    
    /**
     * 获取头像URL
     */
    protected function getAvatarUrl(): string
    {
        if ($this->relationLoaded('avatar') && $this->avatar) {
            return $this->avatar->getUrl();
        }
        
        return route('api.shared.avatar.default', ['name' => $this->name]);
    }
}