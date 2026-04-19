<?php

namespace App\Notifications\Central;

use App\Models\AppVersion;
use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewUpdateAvailableNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Tenant $tenant, public AppVersion $appVersion) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $domain = $this->tenant->domains->first()->domain;
        $url = (request()->secure() ? 'https://' : 'http://').$domain.'/updates';

        return (new MailMessage)
            ->subject('New Update Available: '.$this->appVersion->version.' - JobFlow OMS')
            ->greeting('Hello '.$this->tenant->admin_name.'!')
            ->line('A new update is available for your JobFlow OMS workspace ('.$this->tenant->company_name.').')
            ->line('**Version:** '.$this->appVersion->version)
            ->line('**Release Title:** '.$this->appVersion->title)
            ->line('You can review the release notes and apply this update directly from your workspace dashboard.')
            ->action('Review Update', $url)
            ->line('Thank you for keeping your workspace up to date!');
    }
}
