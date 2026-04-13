<?php

namespace App\Notifications;

use App\Channels\WhatsAppChannel;
use App\Modules\HRIS\Models\LeaveRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewLeaveRequestNotification extends Notification
{
    use Queueable;

    public function __construct(
        public LeaveRequest $leaveRequest,
    ) {}

    public function via(object $notifiable): array
    {
        return [WhatsAppChannel::class, 'database'];
    }

    public function toWhatsApp(object $notifiable): string
    {
        $employee = $this->leaveRequest->user;
        $type = $this->leaveRequest->leaveType?->name ?? $this->leaveRequest->leave_type ?? 'Cuti';
        $start = $this->leaveRequest->start_date->format('d M Y');
        $end = $this->leaveRequest->end_date->format('d M Y');
        $days = $this->leaveRequest->total_days;
        $reason = $this->leaveRequest->reason;

        return "⚠️ *PERMINTAAN CUTI BARU*\n\n"
            . "{$employee->name} mengajukan {$type} dari {$start} s/d {$end} ({$days} hari).\n"
            . "Alasan: {$reason}.\n\n"
            . "Silakan buka Dashboard CoreSpace untuk melakukan Approval.";
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'leave_request',
            'leave_request_id' => $this->leaveRequest->id,
            'employee_id' => $this->leaveRequest->user_id,
            'employee_name' => $this->leaveRequest->user?->name,
            'leave_type' => $this->leaveRequest->leaveType?->name ?? $this->leaveRequest->leave_type,
            'start_date' => $this->leaveRequest->start_date->toDateString(),
            'end_date' => $this->leaveRequest->end_date->toDateString(),
            'total_days' => $this->leaveRequest->total_days,
            'reason' => $this->leaveRequest->reason,
            'message' => "{$this->leaveRequest->user?->name} mengajukan cuti baru.",
        ];
    }
}
