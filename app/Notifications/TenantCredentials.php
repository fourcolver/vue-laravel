<?php

namespace App\Notifications;

use App\Models\Tenant;
use App\Repositories\TemplateRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Class TenantCredentials
 * @package App\Notifications
 */
class TenantCredentials extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var Tenant
     */
    protected $tenant;

    /**
     * TenantCredentials constructor.
     * @param Tenant $tenant
     */
    public function __construct(Tenant $tenant)
    {
        $this->tenant = $tenant;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
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
        $data = $tRepo->getTenantCredentialsParsedTemplate($this->tenant);
        $data['userName'] = $notifiable->name;

        $pdfName = $this->tenant->pdfXFileName();
        if ($data['company'] && $data['company']->blank_pdf) {
            $pdfName = $this->tenant->pdfFileName();
        }
        $disk = \Storage::disk('tenant_credentials');

        return (new MailMessage)
            ->view('mails.sendTenantCredentials', $data)
            ->attachData($disk->get($pdfName), $pdfName)
            ->subject($data['subject']);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [];
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
