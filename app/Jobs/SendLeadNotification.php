<?php

namespace App\Jobs;

use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Mail\Message;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendLeadNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public readonly Lead $lead) {}

    public function handle(): void
    {
        $lead = $this->lead;
        $subject = "New Lead: {$lead->full_name} ({$lead->reference})";

        $phone = $lead->phone ?? 'Not provided';
        $courseName = $lead->course?->title ?? 'No specific course';
        $motivation = $lead->motivation ?? '(none provided)';
        $submittedAt = $lead->created_at?->toFormattedDateString() ?? 'Unknown';

        $body = implode("\n", [
            'A new enquiry has been submitted via the Wenza marketing site.',
            '',
            "Reference:       {$lead->reference}",
            "Name:            {$lead->full_name}",
            "Email:           {$lead->email}",
            "Phone:           {$phone}",
            "Course:          {$courseName}",
            "Referral Source: {$lead->referral_source}",
            "Status:          {$lead->status}",
            '',
            'Motivation / Message:',
            $motivation,
            '',
            "Submitted at: {$submittedAt}",
        ]);

        Mail::raw($body, function (Message $message) use ($subject): void {
            $message
                ->to('hello@wenza.com')
                ->subject($subject);
        });
    }
}
