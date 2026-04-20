<?php

namespace App\Jobs;

use App\Mail\AdminNewLeadNotification;
use App\Models\Lead;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotifyAdminOfNewLead implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Lead $lead
    ) {}

    public function handle(): void
    {
        $adminEmail = config('mail.admin_notification_address') ?? config('mail.from.address');

        try {
            Mail::to($adminEmail)->send(new AdminNewLeadNotification($this->lead));
            Log::info("Admin lead notification email sent successfully for lead [{$this->lead->reference_code}]");
        } catch (Exception $e) {
            Log::error("Failed to send admin lead notification email for lead [{$this->lead->reference_code}]: ".$e->getMessage());
            throw $e;
        }
    }
}
