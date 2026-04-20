<?php

namespace App\Mail;

use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminNewLeadNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Lead $lead
    ) {}

    public function envelope(): Envelope
    {
        $subject = sprintf(
            'New lead: %s — %s',
            $this->lead->full_name,
            $this->lead->course?->title ?? 'General application'
        );

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.admin-new-lead',
            with: [
                'lead' => $this->lead,
                'adminUrl' => config('app.url')."/admin/leads/{$this->lead->id}/edit",
            ],
        );
    }
}
