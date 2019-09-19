<?php

namespace App\Notifications;

use App\Models\ServiceRequest;
use App\Models\User;
use App\Repositories\TemplateRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Class NewTenantRequest
 * @package App\Notifications
 */
class NewTenantRequest extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var ServiceRequest
     */
    protected $serviceRequest;
    /**
     * @var string
     */
    protected $user;
    /**
     * @var string
     */
    protected $subjectUser;

    /**
     * NewTenantRequest constructor.
     * @param ServiceRequest $serviceRequest
     * @param User $user
     * @param User $subjectUser
     */
    public function __construct(ServiceRequest $serviceRequest, User $user, User $subjectUser)
    {
        $this->serviceRequest = $serviceRequest;
        $this->user = $user;
        $this->subjectUser = $subjectUser;
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
        $tRepo = new TemplateRepository(app());
        $data = $tRepo->getNewRequestParsedTemplate($this->serviceRequest, $this->user, $this->subjectUser);
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
            'post_id' => $this->serviceRequest->id,
            'user_name' => $this->serviceRequest->tenant->user->name,
            'fragment' => 'New request opened',
        ];
    }
}
