<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\AccessCode;

class AccessCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public AccessCode $temporaryAccessCode;

    public function __construct(AccessCode $temporaryAccessCode) {
        $this->temporaryAccessCode = $temporaryAccessCode;
    }

    public function build() {
        return $this->subject('Código de verificação - Item Expo')->view('emails.access_code');
    }
}
