<?php

namespace App\Mails;

use App\Models\ServiceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RequestAdded extends Mailable
{
    use Queueable, SerializesModels;

    public $serviceRequest;

    /**
     * Create a new message instance.
     *
     * @param ServiceRequest $serviceRequest
     * @return void
     */
    public function __construct(ServiceRequest $serviceRequest)
    {
        $this->serviceRequest = $serviceRequest;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mails.serviceRequest')
            ->subject(__("A new request was added"))
            ->with([
                'post' => $this->serviceRequest,
                'tenant' => $this->serviceRequest->tenant,
            ]);
    }
}
