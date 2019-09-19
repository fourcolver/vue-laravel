<?php

namespace App\Notifications;

use App\Models\Comment;
use App\Models\Post;
use App\Models\Tenant;
use App\Repositories\TemplateRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

/**
 * Class PostCommented
 * @package App\Notifications
 */
class PostCommented extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var Post
     */
    protected $post;
    /**
     * @var Tenant
     */
    protected $commenter;
    /**
     * @var Comment
     */
    protected $comment;

    /**
     * PostCommented constructor.
     * @param Post $post
     * @param Tenant $commenter
     * @param Comment $comment
     */
    public function __construct(Post $post, Tenant $commenter, Comment $comment)
    {
        $this->post = $post;
        $this->commenter = $commenter;
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
        if ($notifiable->settings && $notifiable->settings->news_notification) {
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
        $data = $tRepo->getPostCommentedParsedTemplate($this->post, $this->commenter->user, $this->comment);
        $data['userName'] = $notifiable->name;

        return (new MailMessage)
            ->view('mails.postCommented', $data)->subject($data['subject']);
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
            'post_id' => $this->post->id,
            'tenant' => $this->commenter->name,
            'comment' => $this->comment->comment,
            'fragment' => Str::limit($this->post->content, 128),
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
