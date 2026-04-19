<?php

namespace App\Notifications\Central;

use App\Models\AppVersion;
use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UpdateAppliedNotification extends Notification implements ShouldQueue
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
        $url = (request()->secure() ? 'https://' : 'http://').$domain.'/dashboard';

        return (new MailMessage)
            ->subject('Update Successfully Applied: '.$this->appVersion->version)
            ->greeting('Hello '.$this->tenant->admin_name.',')
            ->line('Great news! The system update to version **'.$this->appVersion->version.'** has been successfully applied to your workspace.')
            ->line('Your environment is fully up to date and all new features are ready for use.')
            ->action('Go to Dashboard', $url)
            ->line('Thank you for using JobFlow OMS!');
    }
}
