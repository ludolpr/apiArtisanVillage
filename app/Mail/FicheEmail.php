<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FicheEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    use Queueable, SerializesModels;

    public $ficheUrl;

    public function __construct($ficheUrl)
    {
        $this->ficheUrl = $ficheUrl;
    }

    public function build()
    {
        return $this->view('emails.sheetok')
            ->with(['ficheUrl' => $this->ficheUrl]);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Confirmation de création de votre fiche d'entreprise",
        );
    }
    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'sheetok',
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
