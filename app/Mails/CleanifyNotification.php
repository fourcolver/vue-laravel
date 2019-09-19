<?php

namespace App\Mails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Models\ServiceProvider;
use App\Models\ServiceRequest;
use App\Models\CleanifyRequest;

class CleanifyNotification extends Mailable
{
    use Queueable, SerializesModels;

    private $creq;
    public $subject;
    protected $body;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(CleanifyRequest $creq, string $subject, string $body)
    {
        $this->creq = $creq;
        $this->subject = $subject;
        $this->body = $body;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mails.cleanifyRequest')
            ->subject($this->subject)
            ->with([
                'body' => $this->body,
                'subject' => $this->subject,
            ]);
    }
}

