<?php

namespace App\Jobs;

use App\Mail\AdminNewContactMessageNotification;
use App\Models\ContactMessage;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotifyAdminOfNewContactMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public ContactMessage $message
    ) {}

    public function handle(): void
    {
        $adminEmail = config('mail.admin_notification_address') ?? config('mail.from.address');

        try {
            Mail::to($adminEmail)->send(new AdminNewContactMessageNotification($this->message));
            Log::info("Admin contact message notification email sent successfully for message [{$this->message->reference_code}]");
        } catch (Exception $e) {
            Log::error("Failed to send admin contact message notification email for message [{$this->message->reference_code}]: ".$e->getMessage());
            throw $e;
        }
    }
}
