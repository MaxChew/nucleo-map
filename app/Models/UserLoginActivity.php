<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserLoginActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'last_ip',
        'curdate',
        'lat',
        'long',
        'user_agent',
        'device',
        'browser',
        'platform',
        'last_activity',
    ];

    protected $casts = [
        'curdate' => 'date',
        'lat' => 'decimal:8',
        'long' => 'decimal:8',
        'last_activity' => 'datetime',
    ];

    /**
     * 关联到用户模型
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 获取特定IP和日期的记录
     */
    public static function getByIpAndDate(string $ip, string $date)
    {
        return static::where('last_ip', $ip)
                    ->where('curdate', $date)
                    ->first();
    }

    /**
     * 创建或更新登录活动记录
     */
    public static function createOrUpdate(array $data)
    {
        return static::updateOrCreate(
            [
                'last_ip' => $data['last_ip'],
                'curdate' => $data['curdate'],
            ],
            $data
        );
    }
} 