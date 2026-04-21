<?php

namespace App\Mail;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminNewContactMessageNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public ContactMessage $message
    ) {}

    public function envelope(): Envelope
    {
        $subjectLabels = [
            'application_question' => 'Application Question',
            'scholarship_question' => 'Scholarship Question',
            'press_partnerships' => 'Press/Partnerships',
            'other' => 'Other',
        ];

        $subject = sprintf(
            'New contact message — %s',
            $subjectLabels[$this->message->subject] ?? 'Other'
        );

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.admin-new-contact-message',
            with: [
                'message' => $this->message,
                'adminUrl' => config('app.url')."/admin/contact-messages/{$this->message->id}/edit",
            ],
        );
    }
}
