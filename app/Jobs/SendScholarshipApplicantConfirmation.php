<?php

namespace App\Jobs;

use App\Mail\ScholarshipApplicantConfirmation;
use App\Models\ScholarshipApplication;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendScholarshipApplicantConfirmation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public ScholarshipApplication $application
    ) {}

    public function handle(): void
    {
        try {
            Mail::to($this->application->email)->send(new ScholarshipApplicantConfirmation($this->application));
            Log::info("Scholarship applicant confirmation email sent successfully to {$this->application->email} [{$this->application->reference_code}]");
        } catch (Exception $e) {
            Log::error("Failed to send scholarship applicant confirmation email to {$this->application->email}: ".$e->getMessage());
            throw $e;
        }
    }
}
