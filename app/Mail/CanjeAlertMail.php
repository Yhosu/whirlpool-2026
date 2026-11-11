<?php

namespace App\Mail;

use App\Models\Form;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CanjeAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Form $form,
        public array $stockSummary,
        public ?string $attachmentPath = null,
        public ?string $attachmentName = null
    ) {}

    public function envelope(): Envelope
    {
        $isInformal = ($this->form->type === 'informal-market' || $this->form->business_unit === 'MERCADO INFORMAL');
        $canal = $isInformal ? 'MERCADO INFORMAL' : 'ECOMMERCE';
        $premioAmount = $this->form->prize ? (int) $this->form->prize->amount : 0;

        return new Envelope(
            from: new Address(
                config('mail.from.address', 'noreply@whirlpool.bo'),
                config('mail.from.name', 'Promo Whirlpool')
            ),
            subject: "🎁 [CANJE {$canal}] Premio Bs. {$premioAmount} - {$this->form->name}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.canje_alert',
            with: [
                'form' => $this->form,
                'stockSummary' => $this->stockSummary,
            ],
        );
    }

    public function attachments(): array
    {
        $attachments = [];

        if ($this->attachmentPath && file_exists($this->attachmentPath)) {
            $name = $this->attachmentName ?: ('Comprobante_' . preg_replace('/[^0-9]/', '', (string) $this->form->ci_number) . '.jpg');
            $attachments[] = Attachment::fromPath($this->attachmentPath)->as($name);
        }

        return $attachments;
    }
}

