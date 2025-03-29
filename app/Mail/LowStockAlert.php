<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;

class LowStockAlert extends Mailable
{
    use Queueable, SerializesModels;

    public array $data;

    /**
     * Create a new message instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME')),
            to: (User::find(1))->email, // to send the email only to the super admin
            cc: [], // to send a copy to another email address (it will be visible to the recipient)
            bcc: [], // to send a copy to another email address (it will be hidden from the recipient)
            replyTo: [new Address('no-reply@stocksx.com')], // to set the reply-to address (set it to null or 'no-reply' if you don't want to receive replies)
            subject: 'Low Stock Alert',
            tags: ['stocks', 'quantity', 'alert'],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.low-stocks',
            with: [
                'products' => $this->data['products'],
                'user' => $this->data['user'],
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
