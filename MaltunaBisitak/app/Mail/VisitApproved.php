<?php

namespace App\Mail;

use App\Models\Visit;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Confirms the visit with the visitor's personal check-in QR code (AP003, BE001).
 */
class VisitApproved extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public Visit $visit, public string $visitorName, public string $code) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('public.mail.approved.subject'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.visit-approved',
            with: [
                'qrCodePng' => (new PngWriter)->write(new QrCode($this->code, size: 240, margin: 8))->getString(),
            ],
        );
    }
}
