<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;

class JobNotificationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $mailData;

    /**
     * Create a new message instance.
     */
    public function __construct($mailData)
    {
        $this->mailData = $mailData;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = 'Job Notification Email';

        if (!empty($this->mailData['user']?->name)) {
            $subject .= ': ' . $this->mailData['user']->name;
        }

        // CC addresses
        $ccEmails = [];

        if (!empty($this->mailData['user']?->email)) {
            $ccEmails[] = $this->mailData['user']->email;
        }

        // Add CCs from .env
        $ccFromEnv = array_map('trim', explode(',', env('JOB_NOTIFICATION_CC_EMAILS', '')));
        $ccEmails = array_merge($ccEmails, $ccFromEnv);

        return new Envelope(
            subject: $subject,
            cc: $ccEmails,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'email.job-notification-email',
            with: ['mailData' => $this->mailData]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $attachments = [];

        if (!empty($this->mailData['cv'])) {
            // Prepend the folder path
            $relativePath = 'upload/cv/' . $this->mailData['cv'];
            $cvPath = public_path($relativePath);

            if (file_exists($cvPath)) {
                $attachments[] = Attachment::fromPath($cvPath)
                    ->as($this->mailData['cv'])
                    ->withMime('application/pdf');
            }
        }

        return $attachments;
    }
}
