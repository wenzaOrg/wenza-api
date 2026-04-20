<?php

namespace App\Mail;

use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class ApplicantConfirmation extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Lead $lead
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Application Received | Wenza Academy',
        );
    }

    public function content(): Content
    {
        $firstName = Str::before($this->lead->full_name, ' ');

        return new Content(
            markdown: 'emails.applicant-confirmation',
            with: [
                'firstName' => $firstName,
                'programmeName' => $this->lead->course?->title ?? 'our programmes',
                'referenceCode' => $this->lead->reference_code,
                'whatsappUrl' => config('services.engagement.whatsapp_url', 'https://chat.whatsapp.com/placeholder'),
                'calendlyUrl' => config('services.engagement.calendly_url', 'https://calendly.com/wenza/info-session'),
            ],
        );
    }
}
