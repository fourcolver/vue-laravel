<?php

namespace App\Notifications;

use App\Models\Product;
use App\Models\Tenant;
use App\Repositories\TemplateRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class ProductLiked extends Notification implements ShouldQueue
{
    use Queueable;

    protected $product;
    protected $liker;

    /**
     * ProductLiked constructor.
     * @param Product $product
     * @param Tenant $liker
     */
    public function __construct(Product $product, Tenant $liker)
    {
        $this->product = $product;
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
        $data = $tRepo->getProductLikedParsedTemplate($this->product, $this->liker->user);
        $data['userName'] = $notifiable->name;

        return (new MailMessage)
            ->view('mails.productLiked', $data)->subject($data['subject']);
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
            'tenant' => $this->liker->name,
            'fragment' => Str::limit($this->product->content, 128),
        ];
    }

    public function toDatabase($notifiable)
    {
        return $this->toArray($notifiable);
    }
}
