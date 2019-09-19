<?php

namespace App\Notifications;

use App\Models\ServiceRequest;
use App\Repositories\TemplateRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

/**
 * Class RequestDue
 * @package App\Notifications
 */
class RequestDue extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var ServiceRequest
     */
    protected $request;

    /**
     * RequestCommented constructor.
     * @param ServiceRequest $request
     */
    public function __construct(ServiceRequest $request)
    {
        $this->request = $request;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        if ($this->dontSend($notifiable)) {
            return [];
        }

        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        $tRepo = new TemplateRepository(app());
        $data = $tRepo->getRequestDueParsedTemplate($this->request, $notifiable);
        $data['userName'] = $notifiable->name;

        return (new MailMessage)
            ->view('mails.requestDue', $data)->subject($data['subject']);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'request_id' => $this->request->id,
            'fragment' => Str::limit($this->request->title, 128),
        ];
    }

    /**
     * @param $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return $this->toArray($notifiable);
    }

    public function dontSend($notifiable)
    {
        $req = ServiceRequest::find($this->request->id);
        if (!$req) {
            return true;
        }
        if (!$req->due_date) {
            return true;
        }

        $undoneStatuses = [
            ServiceRequest::StatusReceived,
            ServiceRequest::StatusInProcessing,
            ServiceRequest::StatusAssigned,
            ServiceRequest::StatusReactivated,
        ];
        foreach ($undoneStatuses as $s) {
            if ($req->status == $s) {
                return false;
            }
        }

        return true;
    }
}
