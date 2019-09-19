<?php

namespace App\Notifications;

use App\Models\Post;
use App\Models\Tenant;
use App\Repositories\TemplateRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

/**
 * Class PostLiked
 * @package App\Notifications
 */
class PostLiked extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var Post
     */
    protected $post;
    /**
     * @var Tenant
     */
    protected $liker;

    /**
     * PostLiked constructor.
     * @param Post $post
     * @param Tenant $liker
     */
    public function __construct(Post $post, Tenant $liker)
    {
        $this->post = $post;
        $this->liker = $liker;
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
        $data = $tRepo->getPostLikedParsedTemplate($this->post, $this->liker->user);
        $data['userName'] = $notifiable->name;

        return (new MailMessage)
            ->view('mails.postLiked', $data)->subject($data['subject']);
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
            'tenant' => $this->liker->name,
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
