<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactMessage extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public array $contact) {}

    public function build(): static
    {
        return $this->subject('Pesan dari website Batik Rubung Kuning')
            ->replyTo($this->contact['email'], $this->contact['name'])
            ->view('emails.contact');
    }
}
