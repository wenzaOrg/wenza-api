<?php

namespace App\Mail;

use App\Models\ScholarshipApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminNewScholarshipApplicationNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public ScholarshipApplication $application
    ) {}

    public function envelope(): Envelope
    {
        $subject = sprintf(
            'New Scholarship Application: %s %s — %s',
            $this->application->first_name,
            $this->application->last_name,
            $this->application->course?->title ?? 'General'
        );

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.admin-new-scholarship-application',
            with: [
                'application' => $this->application,
                'adminUrl' => config('app.url')."/admin/scholarship-applications/{$this->application->id}/edit",
            ],
        );
    }
}
