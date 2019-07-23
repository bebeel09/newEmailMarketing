<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class spamMailing extends Mailable
{
    use Queueable, SerializesModels;


    public $sender;
    public $view;
    public $data;




    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($sender, $view, $dataView)
    {
        $this->sender = $sender;
        $this->view=$view;
        $this->data=$dataView;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from($this->sender)
                    ->view('template.'.$this->view)
                    ->with(
                      [
                            'contact' => $this->data,
                      ]);
    }
}
