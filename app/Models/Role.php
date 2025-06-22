<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Maxxidev\Author\Traits\CaptureAuthors;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use CaptureAuthors;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'guard_name',
        'display_name',
        'description',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Override the users() relationship to prevent class name conflicts
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users(): BelongsToMany
    {
        // 直接使用配置文件中的用户模型，不进行复杂的guard检测
        $userModel = config('auth.providers.users.model', \App\Models\User::class);
        
        if (!$userModel || !class_exists($userModel)) {
            throw new \InvalidArgumentException("User model [{$userModel}] not found or invalid");
        }

        return $this->morphedByMany(
            $userModel,
            'model',
            config('permission.table_names.model_has_roles'),
            config('permission.column_names.role_pivot_key') ?: 'role_id',
            config('permission.column_names.model_morph_key') ?: 'model_id'
        );
    }
}
