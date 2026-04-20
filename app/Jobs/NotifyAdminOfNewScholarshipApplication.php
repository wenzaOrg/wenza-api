<?php

namespace App\Jobs;

use App\Mail\AdminNewScholarshipApplicationNotification;
use App\Models\ScholarshipApplication;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotifyAdminOfNewScholarshipApplication implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public ScholarshipApplication $application
    ) {}

    public function handle(): void
    {
        $adminEmail = config('mail.admin_scholarship_notification')
            ?? config('mail.admin_notification_address')
            ?? config('mail.from.address');

        try {
            Mail::to($adminEmail)->send(new AdminNewScholarshipApplicationNotification($this->application));
            Log::info("Admin scholarship application notification email sent successfully for [{$this->application->reference_code}]");
        } catch (Exception $e) {
            Log::error("Failed to send admin scholarship application notification email for [{$this->application->reference_code}]: ".$e->getMessage());
            throw $e;
        }
    }
}
