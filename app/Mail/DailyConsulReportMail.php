<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DailyConsulReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $dateStart,
        public string $dateEnd,
        public array $data,
        public string $excelPath
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('mail.from.address'), 'Promo Consul'),
            subject: 'Reporte Diario Consul - '.$this->dateStart,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.daily_consul_report',
            with: array_merge($this->data, [
                'dateStart' => $this->dateStart,
                'dateEnd' => $this->dateEnd,
            ]),
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->excelPath)
                ->as('Reporte_Consul_'.$this->dateStart.'.xlsx')
                ->withMime('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'),
        ];
    }
}
