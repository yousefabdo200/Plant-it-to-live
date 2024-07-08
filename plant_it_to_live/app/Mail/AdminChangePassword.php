<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminChangePassword extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    private $name , $token;
    public function __construct($name,$token)
    {
        //
        $this->name=$name;
        $this->token=$token;
    }
    public function build()
    {
        return $this->subject('Admin Reset Password')
                    ->view('AdminResetpassword')
                    ->with([
                        'name' => $this->name,
                        'token' => $this->token,
                    ]);
    }

}
