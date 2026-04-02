<?php

namespace App\Notifications\Central;

use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TenantApprovedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Tenant $tenant)
    {
        //
    }

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
        $url = (request()->secure() ? 'https://' : 'http://') . $domain . '/login';

        return (new MailMessage)
            ->subject('Welcome to JobFlow OMS - Your Workspace is Ready!')
            ->greeting('Hello ' . $this->tenant->admin_name . '!')
            ->line('Great news! Your workspace application for **' . $this->tenant->company_name . '** has been approved.')
            ->line('You can now access your isolated environment and start managing your operations.')
            ->action('Access Your Workspace', $url)
            ->line('Thank you for choosing JobFlow OMS for your operations management!');
    }
}
