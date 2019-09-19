<?php

namespace App\Notifications;

use App\Models\Post;
use App\Models\User;
use App\Repositories\TemplateRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

/**
 * Class NewTenantPost
 * @package App\Notifications
 */
class NewTenantPost extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var Post
     */
    protected $post;
    /**
     * @var User
     */
    protected $user;

    /**
     * NewTenantPost constructor.
     * @param Post $post
     * @param User $user
     */
    public function __construct(Post $post, User $user)
    {
        $this->post = $post;
        $this->user = $user;
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
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $tRepo = new TemplateRepository(app());
        $data = $tRepo->getNewPostParsedTemplate($this->post, $this->user);
        $data['userName'] = $notifiable->name;

        return (new MailMessage)
            ->view('mails.postAddedByTenant', $data)->subject($data['subject']);
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
