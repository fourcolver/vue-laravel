<?php

namespace App\Notifications;

use App\Models\ServiceRequest;
use App\Models\User;
use App\Repositories\TemplateRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\Models\Media;

/**
 * Class RequestMedia
 * @package App\Notifications
 */
class RequestMedia extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var ServiceRequest
     */
    protected $request;
    /**
     * @var User
     */
    protected $uploader;
    /**
     * @var Media
     */
    protected $media;

    /**
     * RequestMedia constructor.
     * @param ServiceRequest $request
     * @param User $uploader
     * @param Media $media
     */
    public function __construct(ServiceRequest $request, User $uploader, Media $media)
    {
        $this->request = $request;
        $this->uploader = $uploader;
        $this->media = $media;
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
        $data = $tRepo->getRequestMediaParsedTemplate($this->request, $this->uploader, $notifiable, $this->media);
        $data['userName'] = $notifiable->name;

        return (new MailMessage)
            ->view('mails.requestMedia', $data)->subject($data['subject'])->attach($data['media']->getPath());
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
            'uploader' => $this->uploader,
            'media' => $this->media,
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
