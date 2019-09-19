<?php

namespace App\Notifications;

use App\Models\Comment;
use App\Models\Product;
use App\Models\Tenant;
use App\Repositories\TemplateRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

/**
 * Class ProductCommented
 * @package App\Notifications
 */
class ProductCommented extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var Product
     */
    protected $product;
    /**
     * @var Tenant
     */
    protected $commenter;
    /**
     * @var Comment
     */
    protected $comment;

    /**
     * ProductCommented constructor.
     * @param Product $product
     * @param Tenant $commenter
     * @param Comment $comment
     */
    public function __construct(Product $product, Tenant $commenter, Comment $comment)
    {
        $this->product = $product;
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
        if ($notifiable->settings && $notifiable->settings->marketplace_notification) {
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
        $data = $tRepo->getProductCommentedParsedTemplate($this->product, $this->commenter->user, $this->comment);
        $data['userName'] = $notifiable->name;

        return (new MailMessage)
            ->view('mails.productCommented', $data)->subject($data['subject']);
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
            'product_id' => $this->product->id,
            'tenant' => $this->commenter->name,
            'comment' => $this->comment->comment,
            'fragment' => Str::limit($this->product->title, 128),
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
