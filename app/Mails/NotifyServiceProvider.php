<?php

namespace App\Mails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Models\ServiceProvider;
use App\Models\ServiceRequest;
use App\Models\User;

class NotifyServiceProvider extends Mailable
{
    use Queueable, SerializesModels;

    private $provider;
    private $request;
    private $mailDetails;
    private $receivingUser;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(
        ServiceProvider $sp,
        ServiceRequest $sr,
        array $mailDetails, $user = null)
    {
        $this->provider = $sp;
        $this->request = $sr;
        $this->mailDetails = $mailDetails;
        $this->receivingUser = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if ($this->receivingUser) {
            $this->receivingUser->redirect = "/admin/requests/" . $this->request->id;
        }
        return $this->view('mails.notifyServiceProvider')
            ->subject($this->mailDetails['title'] ?? "")
            ->with([
                'provider' => $this->provider,
                'details' => $this->mailDetails,
                'user' => $this->receivingUser,
            ]);
    }
}
