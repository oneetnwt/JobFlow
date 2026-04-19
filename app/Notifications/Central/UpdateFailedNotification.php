<?php

namespace App\Notifications\Central;

use App\Models\AppVersion;
use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UpdateFailedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Tenant $tenant,
        public AppVersion $appVersion,
        public string $errorMessage
    ) {}

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
            ->subject('Urgent: Update Failed for '.$this->tenant->company_name)
            ->greeting('Hello '.$this->tenant->admin_name.',')
            ->line('An error occurred while attempting to apply the system update **'.$this->appVersion->version.'**.')
            ->line('**Error Details:**')
            ->line('`'.$this->errorMessage.'`')
            ->line('Our administrative team has been notified. You can retry the update or contact support if the issue persists.')
            ->action('Review Status', $url)
            ->line('We apologize for the inconvenience.');
    }
}
