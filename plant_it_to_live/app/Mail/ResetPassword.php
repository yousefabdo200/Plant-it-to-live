<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ResetPassword extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    private $token,$name;
    public function __construct($name,$token)
    {
        $this->token=$token;
        $this->name=$name;

    }
    public function build()
    {
        return $this->subject('User Reset Password')
                    ->view('Resetpassword')
                    ->with([
                        'name' => $this->name,
                        'token' => $this->token,
                    ]);
    }
    /**
     * Get the message envelope.
     */
    /*
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reset Password',
        );
    }

    /**
     * Get the message content definition.
     */
    /*
    public function content(): Content
    {
        return new Content(
            view: 'view.name',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
/*    public function attachments(): array
    {
        return [];
    }*/
}
