<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactMail extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $email;
    public $subject;
    public $messageBody;

    /**
     * Create a new message instance.
     */
    public function __construct($name, $email, $subject, $messageBody)
    {
        $this->name = $name;
        $this->email = $email;
        $this->subject = $subject;
        $this->messageBody = $messageBody;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->from($this->email, $this->name)
            ->subject("Contact Form: " . $this->subject)
            ->view('emails.contact');
    }
}
