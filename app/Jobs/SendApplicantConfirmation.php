<?php

namespace App\Jobs;

use App\Mail\ApplicantConfirmation;
use App\Models\Lead;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendApplicantConfirmation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Lead $lead
    ) {}

    public function handle(): void
    {
        try {
            Mail::to($this->lead->email)->send(new ApplicantConfirmation($this->lead));
            Log::info("Applicant confirmation email sent successfully to {$this->lead->email} [{$this->lead->reference_code}]");
        } catch (Exception $e) {
            Log::error("Failed to send applicant confirmation email to {$this->lead->email}: ".$e->getMessage());
            throw $e;
        }
    }
}
