<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ServerErrorMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @param  array<string, mixed>  $errorData
     */
    public function __construct(
        public array $errorData
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $appName = config('app.name', 'Whirlpool');
        $messageSnippet = $this->errorData['message_short'] ?? 'Error no especificado';

        return new Envelope(
            from: new Address(
                config('mail.from.address', 'whirlpool@contact.9lives.one'),
                config('mail.from.name', $appName)
            ),
            subject: "🚨 [{$appName} - 500] {$messageSnippet}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.server-error',
            with: [
                'error' => $this->errorData,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
