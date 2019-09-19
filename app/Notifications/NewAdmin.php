<?php

namespace App\Notifications;

use App\Models\User;
use App\Repositories\TemplateRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\InteractsWithQueue;

/**
 * Class NewAdmin
 * @package App\Notifications
 */
class NewAdmin extends Notification implements ShouldQueue
{
    use Queueable, InteractsWithQueue;

    public $tries = 3;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var User
     */
    protected $subjectUser;

    /**
     * NewAdmin constructor.
     * @param User $user
     * @param User $subjectUser
     */
    public function __construct(User $user, User $subjectUser)
    {
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
        return ['mail'];
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
        $data = $tRepo->getUserNewAdminTemplate($this->user, $this->subjectUser);

        return (new MailMessage)
            ->view('mails.users.userPasswordReset', $data)
            ->subject($data['subject']);
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
            //
        ];
    }
}
