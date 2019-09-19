<?php

namespace App\Notifications;

use App\Models\PasswordReset;
use App\Models\User;
use App\Repositories\TemplateRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Class PasswordResetRequest
 * @package App\Notifications
 */
class PasswordResetRequest extends Notification implements ShouldQueue
{
    use Queueable;

    protected $user;
    protected $pwReset;

    /**
     * PasswordResetRequest constructor.
     * @param User $user
     * @param PasswordReset|null $pwReset
     */
    public function __construct(User $user, PasswordReset $pwReset)
    {
        $this->user = $user;
        $this->pwReset = $pwReset;
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
        $data = $tRepo->getUserResetPasswordTemplate($this->user, $this->pwReset);
        $data['userName'] = $notifiable->name;

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
