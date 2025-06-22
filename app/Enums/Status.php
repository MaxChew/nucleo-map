<?php

namespace App\Enums;


final class Status extends Enum
{
    const NEW = 'NEW';
    const ACTIVE = 'ACTIVE';
    const DROP = 'DROP';
    const REF = 'REF';
    const DELETED = 'DELETED';
    const COMPLETED = 'COMPLETED';
    const REDRAW = 'REDRAW';
    const INPROGRESS = 'IN_PROGRESS';
    const REJECTED = 'REJECTED';
    const CONFIRM = 'CONFIRM';
    const WAITING = 'WAITING';
    const PENDING = 'PENDING';
    const UNVERIFIED = 'UNVERIFIED';
    const DONE = 'DONE';
    const EXPIRED = 'EXPIRED';
    const RESCHEDULING = 'RESCHEDULING';
    const APPROVED = 'APPROVED';
    const READY_GENERATING = 'READY_GENERATING';
    const GENERATING = 'GENERATING';
    const LIVE = 'LIVE';
    const ERROR = 'ERROR';
    const REJECTED_BY_TUTOR = 'REJECTED_BY_TUTOR';
    const PENDING_REPORT = 'PENDING_REPORT';
    const PENDING_APPROVED = 'PENDING_APPROVED';
    const PENDING_PAYMENT = 'PENDING_PAYMENT';
    const PAID = 'PAID';

    const CLASSNAME = [
        self::NEW => 'blue-500',                 // 新建
        self::ACTIVE => 'green-500',             // 激活
        'inactive' => 'red-500',                 // 停用
        self::DROP => 'gray-400',                // 废弃
        self::REF => 'indigo-500',               // 引用
        self::DELETED => 'red-700',              // 删除
        self::COMPLETED => 'gray-700',           // 完成
        self::REDRAW => 'blue-400',              // 重绘
        self::INPROGRESS => 'amber-500',         // 进行中
        self::REJECTED => 'red-600',             // 拒绝
        self::CONFIRM => 'blue-600',             // 确认
        self::WAITING => 'yellow-500',           // 等待
        self::PENDING => 'amber-500',            // 待处理
        self::UNVERIFIED => 'red-500',           // 未验证
        self::DONE => 'gray-600',                // 已完成
        self::EXPIRED => 'gray-500',             // 已过期
        self::RESCHEDULING => 'amber-600',       // 重新安排中
        self::APPROVED => 'green-600',           // 已批准
        self::READY_GENERATING => 'blue-400',    // 准备生成
        self::GENERATING => 'blue-600',          // 生成中
        self::LIVE => 'green-600',               // 已上线
        self::ERROR => 'red-800',                // 错误
        self::REJECTED_BY_TUTOR => 'rose-600',   // 导师拒绝
        self::PENDING_REPORT => 'yellow-500',    // 等待报告
        self::PENDING_APPROVED => 'amber-600',   // 等待批准
        self::PENDING_PAYMENT => 'amber-700',    // 等待支付
        self::PAID => 'teal-600'                 // 已支付
    ];

    const LIST = [
        self::NEW,
        self::ACTIVE,
        self::DROP,
        self::COMPLETED,
    ];

    const MAP = [
        self::NEW => 'New',
        self::ACTIVE => 'Active',
        self::DROP => 'Drop',
        self::COMPLETED => 'Completed',
        self::GENERATING => 'Generating',
        self::LIVE => 'Live',
    ];


    //Client registration 
    const CLIENT_REGISTRATION_LIST = [
        self::PENDING,
        self::COMPLETED,
    ];

    const CLIENT_REGISTRATION_MAP = [
        self::PENDING => 'PENDING',
        self::COMPLETED => 'COMPLETED',
    ];

    //Medical Centers
    const CENTER_STATUS_LIST = [
        self::ACTIVE,
        'inactive',
    ];

    const CENTER_STATUS_MAP = [
        self::ACTIVE => 'Active Centers',
        'inactive' => 'Inactive Centers',
    ];

    //Roles Management
    const ROLE_STATUS_LIST = [
        'with_permissions',
        'without_permissions',
    ];

    const ROLE_STATUS_MAP = [
        'with_permissions' => 'With Permissions',
        'without_permissions' => 'Without Permissions',
    ];


    //Course 
    const COURSE_STATUS_LIST = [
        self::PENDING, //Once Course Created after input subject/tutor/student, will be this status
        self::PENDING_APPROVED, //After admin input all class, will become this status
        self::PENDING_PAYMENT, //after tutor approve, course will wait for payment
        self::READY_GENERATING, //after payment, course will be ready for generating lessons, means pending to live
        self::LIVE,  //after generating lessons, course will be live
        self::COMPLETED,  //after month end. will calcuate carry foward bal and move to this
        self::REJECTED,  //when tutor reject the requested course, will become this status
        self::EXPIRED,  //when tutor does not approve/reject the requested course, will become this status
    ];

    const COURSE_STATUS_MAP = [
        self::PENDING => 'Pending Assign Class By Admin',
        self::PENDING_APPROVED => 'Pending Acceptance By Tutor',
        self::PENDING_PAYMENT => 'Pending Payment by Client',
        self::READY_GENERATING => 'Pending To Live',
        self::LIVE => 'Live',
        self::COMPLETED => 'Completed',
        self::REJECTED => 'Rejected By Tutor',
        self::EXPIRED => 'Expired',
    ];

    const COURSE_STATUS_MEANING_MAP = [
        self::PENDING => 'The course has been created and is awaiting further input of subject, tutor, and student details.',
        self::PENDING_APPROVED => 'All class details have been input by the admin, and the course is awaiting tutor approval.',
        self::PENDING_PAYMENT => 'The tutor has approved the course. It is now waiting for payment to proceed.',
        self::READY_GENERATING => 'Payment has been completed. The course is ready for generating lessons and pending activation.',
        self::LIVE => 'Lessons have been generated and the course is now active.',
        self::COMPLETED => 'The course has ended for the month. Any remaining balance hours will be carried forward.',
        self::REJECTED => 'The tutor has rejected the course request.',
        self::EXPIRED => 'The tutor did not approve or reject the course request within the required timeframe, so it has expired.',
    ];

    const COURSE_COMPLETED_STATUS_LIST = [
        self::LIVE,
        self::COMPLETED,
        self::REJECTED,
        self::EXPIRED,
    ];

    const COURSE_INVALID_STATUS_LIST = [
        self::REJECTED,
        self::EXPIRED,
    ];

    const COURSE_READY_LESSON_STATUS_LIST = [
        self::READY_GENERATING,
        self::LIVE,
    ];

    const COURSE_DRAFT_SCHEDULE_LIST = [
        self::PENDING,
        self::PENDING_APPROVED,
        self::PENDING_PAYMENT,
        self::READY_GENERATING,
        self::REJECTED,
        self::EXPIRED,
    ];


    //Lesson 
    const LESSON_STATUS_LIST = [
        self::ACTIVE, //(Only For First Class)
        self::LIVE, //When Class is Live, means now the class is running at this time
        self::DONE, //When Class ended it will become this status

        self::RESCHEDULING, //(Only For First Class when rescheduling is in prcess)
        self::PENDING, //(Only For Rescheduled Class)
        self::REJECTED, //(Only For Rescheduled Class)

        self::PENDING_REPORT, //Once Class is done, it will wait for report submission
        self::COMPLETED, //Only For First Class when rescheduling process done)
        self::DROP, //Canceled . when report no submission or when client/tutor didn't approve/reject the rescheduled class
        self::DELETED, //DELETED- When Regenerate course, those previously generated lesson will be deleted
    ];

    const LESSON_STATUS_MAP = [
        self::ACTIVE => 'Active',
        self::LIVE => 'Live',
        self::DONE => 'Done',

        self::RESCHEDULING => 'Rescheduling',
        self::PENDING => 'Pending Approve Reschedule',
        self::REJECTED => 'Rejected',

        self::PENDING_REPORT => 'Pending Report',
        self::COMPLETED => 'Completed',
        self::DROP => 'Drop',
        self::DELETED => 'Deleted',
    ];

    const LESSON_STATUS_MEANING_MAP = [
        self::ACTIVE => 'This is the normal and currently active class.',
        self::LIVE => "The class is currently in progress.",
        self::DONE => "The class has ended. and report is submitted.",

        self::RESCHEDULING => 'The first(normal) class is being rescheduled.',
        self::PENDING => 'This class has been rescheduled by client/tutor and is waiting for confirmation by client/tutor.',
        self::REJECTED => 'The rescheduled class has been rejected by client/tutor.',

        self::PENDING_REPORT => 'The class has ended and is waiting for the tutor to submit the report.',
        self::COMPLETED => 'The rescheduled class is confirmed by client/tutor.',
        self::DROP => 'The class was canceled due to no report submission or no approval/rejection for the rescheduled class by the client or tutor.',
        self::DELETED => 'The previous lesson was deleted when the course was regenerated.',
    ];

    const LESSON_INVALID_STATUS_LIST = [
        self::DROP,
        self::DELETED,
        self::REJECTED,
        self::RESCHEDULING,
    ];


    //Invoice 
    const INVOICE_STATUS_LIST = [
        self::PENDING, // Once Invoice Create, will get this status and wait client payment
        self::PAID, //Once Payment Done, will get this status
        self::EXPIRED, //After Due Date, will get this status
    ];

    const INVOICE_STATUS_MAP = [
        self::PENDING => 'Pending',
        self::PAID => 'Paid',
        self::EXPIRED => 'Expired',
    ];


    //Payment
    const PAYMENT_STATUS_LIST = [
        self::PENDING, // Once Payment Create, 
        self::PENDING_PAYMENT, // Once add into invoice, will get this status
        self::COMPLETED, //Once Payment Done, will get this status
    ];


    //Report
    const REPORT_STATUS_LIST = [
        self::PENDING_REPORT,
        self::PENDING,
        self::APPROVED,
        self::REJECTED,
        self::EXPIRED,
    ];

    const REPORT_STATUS_MAP = [
        self::PENDING_REPORT => 'Pending Submission By Tutor',
        self::PENDING => 'Waiting For Client Update',
        self::APPROVED => 'Approved By Client',
        self::REJECTED => 'Rejected By Client',
        self::EXPIRED => 'Expired',
    ];

    const REPORT_STATUS_MEANING_MAP = [
        self::PENDING_REPORT => 'The tutor has not yet submitted the report card.',
        self::PENDING => "The report has been submitted by the tutor and is awaiting client's approval.",
        self::APPROVED => 'The client has approved the report, and no further action is needed.',
        self::REJECTED => 'The client has rejected the report, and no further action is needed.',
        self::EXPIRED => 'The report was not submitted by tutor or approved/rejected by client within the required timeframe and is now expired.',
    ];

    const REPORT_COMPLETED_STATUS_LIST = [
        self::APPROVED,
        self::REJECTED,
        self::EXPIRED,
    ];


    //Wallet
    const WALLET_STATUS_LIST = [
        self::ACTIVE,
        self::EXPIRED,
    ];

    const WALLET_STATUS_MAP = [
        self::ACTIVE => 'Active',
        self::EXPIRED => 'Expired',
    ];


    //Receipt
    const RECEIPT_STATUS_LIST = [
        self::PENDING,
        self::PAID,
        self::EXPIRED,
    ];

    const RECEIPT_STATUS_MAP = [
        self::PENDING => 'Pending',
        self::PAID => 'Paid',
        self::EXPIRED => 'Expired',
    ];

    const CLIENT_STATUS_MAP = [
        self::PENDING => 'Pending Reigstration Fees',
        self::ACTIVE => 'Active',
    ];


    public static function getSummary()
    {
        return array_merge(self::MAP, [self::DELETED => 'Deleted']);
    }

    public static function getStatusMeaning($statusMap, $statusMeaningMap)
    {
        $result = [];

        foreach ($statusMap as $key => $label) {
            $result[$key] = [
                'label' => $label,
                'meaning' => $statusMeaningMap[$key] ?? '',
            ];
        }

        return $result;
    }
}
