<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserAccountActivation extends Mailable
{
    use Queueable, SerializesModels;

    protected $user, $token;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $token)
    {
        $this->user = $user;
        $this->token = $token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('User Account Activation')
                    ->view('user_account_activation')
                    ->with([
                        'user' => $this->user,
                        'token' => $this->token,
                    ]);
    }
}
