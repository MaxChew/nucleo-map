<?php

namespace App\Enums;

/*
	•	DRAFT: 初始状态，记录还在编辑中。
	•	PENDING: 出勤记录已经提交，等待进一步处理。
	•	EXPIRED: 出勤记录因为某些原因过期。
	•	VERIFIED: 出勤记录已经过验证和确认。
	•	REJECTED: 出勤记录被审核拒绝。
	•	APPROVED: 出勤记录已经被正式批准。
	•	CANCELLED: 出勤记录已被取消。
*/

final class AttendanceStatus extends Enum
{
    const DRAFT = 'DRAFT';
    const PENDING = 'PENDING';
    const EXPIRED = 'EXPIRED';
    const VERIFIED = 'VERIFIED';
    const REJECTED = 'REJECTED';
    const APPROVED = 'APPROVED';
    const CANCELLED = 'CANCELLED';

    const CLASSNAME = [
        self::DRAFT => 'text-gray-600',
        self::PENDING => 'text-yellow-600',
        self::EXPIRED => 'text-red-600',
        self::VERIFIED => 'text-green-600',
        self::REJECTED => 'text-red-600',
        self::APPROVED => 'text-blue-600',
        self::CANCELLED => 'text-gray-600',
    ];

    const NAME = [
        self::DRAFT => 'Draft',
        self::PENDING => 'Pending',
        self::EXPIRED => 'Expired',
        self::VERIFIED => 'Verified',
        self::REJECTED => 'Rejected',
        self::APPROVED => 'Approved',
        self::CANCELLED => 'Cancelled',
    ];

    const LIST = [
        self::DRAFT,
        self::PENDING,
        self::EXPIRED,
        self::VERIFIED,
        self::REJECTED,
        self::APPROVED,
        self::CANCELLED,
    ];

    const LIST_ACTIVE = [
        self::PENDING,
        self::VERIFIED,
        self::APPROVED,
    ];

    const MAP = [
        self::DRAFT => 'DRAFT',
        self::PENDING => 'PENDING',
        self::EXPIRED => 'EXPIRED',
        self::VERIFIED => 'VERIFIED',
        self::REJECTED => 'REJECTED',
        self::APPROVED => 'APPROVED',
        self::CANCELLED => 'CANCELLED',
    ];
}