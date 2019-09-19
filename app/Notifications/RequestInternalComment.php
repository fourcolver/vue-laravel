<?php

namespace App\Notifications;

use App\Models\Comment;
use App\Models\ServiceRequest;
use App\Models\User;
use App\Repositories\TemplateRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Class RequestInternalComment
 * @package App\Notifications
 */
class RequestInternalComment extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var ServiceRequest
     */
    protected $sr;
    /**
     * @var Comment
     */
    protected $comment;
    /**
     * @var User
     */
    protected $receiver;

    /**
     * RequestInternalComment constructor.
     * @param ServiceRequest $sr
     * @param Comment $comment
     * @param User $receiver
     */
    public function __construct(ServiceRequest $sr, Comment $comment, User $receiver)
    {
        $this->sr = $sr;
        $this->comment = $comment;
        $this->receiver = $receiver;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        if ($notifiable->settings && $notifiable->settings->service_notification) {
            return ['database', 'mail'];
        }

        return ['database'];
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
        $data = $tRepo->getRequestInternalCommentParsedTemplate($this->sr, $this->comment, $this->comment->user, $this->receiver);
        $data['userName'] = $notifiable->name;

        return (new MailMessage)
            ->view('mails.requestInternalComment', $data)->subject($data['subject']);
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
            'request_id' => $this->sr->id,
            'comment' => $this->comment->comment,
            'sender' => $this->comment->user->name,
            'receiver' => $this->receiver->name,
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
}
