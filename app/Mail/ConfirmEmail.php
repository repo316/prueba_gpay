<?php

namespace App\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ConfirmEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $subject='';
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(public string $name='',public string $userToken='')
    {
        $this->subject = 'Correo confirmacion token '.Carbon::now()->format('d-m-Y');
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('email.send_confirm_email');
    }
}
