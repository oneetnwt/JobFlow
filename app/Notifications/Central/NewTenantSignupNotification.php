<?php

namespace App\Notifications\Central;

use App\Models\Plan;
use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewTenantSignupNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Tenant $tenant, public ?Plan $plan)
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
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("New Signup: {$this->tenant->company_name}")
            ->greeting('New Workspace Created')
            ->line('A new tenant workspace has just registered.')
            ->line("Company: {$this->tenant->company_name}")
            ->line("Subdomain: {$this->tenant->subdomain}")
            ->line('Plan chosen: '.($this->plan ? $this->plan->name : 'N/A'))
            ->action('View Tenant Details', route('admin.tenants.show', $this->tenant))
            ->line('No action is required. This is for informational purposes only.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'tenant_id' => $this->tenant->id,
            'company_name' => $this->tenant->company_name,
            'plan_name' => $this->plan ? $this->plan->name : 'N/A',
            'message' => "New tenant signup: {$this->tenant->company_name}",
        ];
    }
}
