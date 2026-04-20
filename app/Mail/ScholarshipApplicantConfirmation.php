<?php

namespace App\Mail;

use App\Models\ScholarshipApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ScholarshipApplicantConfirmation extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public ScholarshipApplication $application
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Scholarship Application Received | Wenza Academy',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.scholarship-applicant-confirmation',
            with: [
                'firstName' => $this->application->first_name,
                'programmeName' => $this->application->course?->title ?? 'our programmes',
                'referenceCode' => $this->application->reference_code,
                'whatsappUrl' => config('services.engagement.whatsapp_url', 'https://chat.whatsapp.com/placeholder'),
                'calendlyUrl' => config('services.engagement.calendly_url', 'https://calendly.com/wenza/info-session'),
            ],
        );
    }
}
