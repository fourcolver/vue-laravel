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
use Illuminate\Support\Str;

/**
 * Class RequestCommented
 * @package App\Notifications
 */
class RequestCommented extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var ServiceRequest
     */
    protected $request;
    /**
     * @var User
     */
    protected $user;
    /**
     * @var Comment
     */
    protected $comment;

    /**
     * RequestCommented constructor.
     * @param ServiceRequest $request
     * @param User $user
     * @param Comment $comment
     */
    public function __construct(ServiceRequest $request, User $user, Comment $comment)
    {
        $this->request = $request;
        $this->user = $user;
        $this->comment = $comment;
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
        $data = $tRepo->getRequestCommentedParsedTemplate($this->request, $this->user, $this->comment);
        $data['userName'] = $notifiable->name;

        return (new MailMessage)
            ->view('mails.requestCommented', $data)->subject($data['subject']);
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
            'user' => $this->comment->user->name,
            'comment' => $this->comment->comment,
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
}
