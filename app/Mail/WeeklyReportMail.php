<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class WeeklyReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public Carbon $start;

    public Carbon $end;

    public array $totals;

    public array $excelPaths;

    /**
     * Create a new message instance.
     */
    public function __construct(Carbon $start, Carbon $end, array $totals, array $excelPaths)
    {
        $this->start = $start;
        $this->end = $end;
        $this->totals = $totals;
        $this->excelPaths = $excelPaths;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reporte Semanal de Participantes - '.env('APP_NAME', 'Admin Panel'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.weekly_report',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        $attachments = [];

        foreach ($this->excelPaths as $path) {
            $attachments[] = Attachment::fromPath($path);
        }

        return $attachments;
    }
}
