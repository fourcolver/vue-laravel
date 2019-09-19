<?php

namespace App\Notifications;

use App\Models\Post;
use App\Repositories\TemplateRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

/**
 * Class PinnedPostPublished
 * @package App\Notifications
 */
class PinnedPostPublished extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var Post
     */
    protected $post;

    /**
     * PinnedPostPublished constructor.
     * @param Post $post
     */
    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'mail'];
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
        $data = $tRepo->getPinnedPostParsedTemplate($this->post, $notifiable);
        $data['userName'] = $notifiable->name;

        return (new MailMessage)->view('mails.pinnedPostPublished', $data)->subject($data['subject']);
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
            'post_type' => $this->post->type,
            'user_name' => $this->post->user->name,
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
