<?php

namespace App\Mail;

use App\Models\Repayment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;

class RepaymentConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $repayment;

    /**
     * Create a new message instance.
     */
    public function __construct(Repayment $repayment)
    {
        $this->repayment = $repayment;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            replyTo: [
                new Address(config('mail.from.address'), config('mail.from.name')),
            ],
            subject: 'Loan Repayment Confirmation - Reference: ' . $this->repayment->reference_no,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.repayment-confirmation',
            with: [
                'repayment' => $this->repayment,
                'loan' => $this->repayment->loan,
                'client' => $this->repayment->loan->client,
                'branch' => $this->repayment->loan->branch,
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