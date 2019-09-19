<?php

namespace App\Notifications;

use App\Models\ServiceRequest;
use App\Models\User;
use App\Repositories\TemplateRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\InteractsWithQueue;

/**
 * Class StatusChangedRequest
 * @package App\Notifications
 */
class StatusChangedRequest extends Notification implements ShouldQueue
{
    use Queueable, InteractsWithQueue;

    /**
     * @var int
     */
    public $tries = 3;

    /**
     * @var ServiceRequest
     */
    protected $request;
    /**
     * @var ServiceRequest
     */
    protected $originalRequest;
    /**
     * @var
     */
    protected $originalStatus;
    /**
     * @var User
     */
    protected $user;

    /**
     * StatusChangedRequest constructor.
     * @param ServiceRequest $request
     * @param ServiceRequest $originalRequest
     * @param User $user
     */
    public function __construct(ServiceRequest $request, ServiceRequest $originalRequest, User $user)
    {
        $this->request = $request;
        $this->user = $user;
        $this->originalRequest = $originalRequest;
        $this->originalStatus = $originalRequest->status;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $this->originalRequest->status = $this->originalStatus;

        $tRepo = new TemplateRepository(app());
        $data = $tRepo->getRequestStatusChangedParsedTemplate($this->request, $this->originalRequest, $this->user);
        $data['userName'] = $notifiable->name;

        return (new MailMessage)
            ->view('mails.request', $data)->subject($data['subject']);
    }

    /**
     * @param $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return $this->toArray($notifiable);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'post_id' => $this->request->id,
            'user_name' => $notifiable->name,
            'fragment' => sprintf('Request: %s status changed to:%',
                $this->request->title,
                $this->request->status
            )
        ];
    }
}
