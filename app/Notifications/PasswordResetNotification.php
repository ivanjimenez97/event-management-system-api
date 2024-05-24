<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class PasswordResetNotification extends Notification
{
    use Queueable;

    /** @var string */
    private string $token;

    /** @var User */
    private $user;
    /**
     * Create a new notification instance.
     */
    public function __construct($user, string $token)
    {
        $this->user = $user;
        $this->token = $token;
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
        $name = $notifiable instanceof User
            ? $notifiable->name
            : 'Recipient';

        return (new MailMessage)
            ->subject($name . ' - Password Reset Notification')
            ->greeting("*Dear {$name}!*")
            ->line('We Received a password request for Your Account. To reset Your password open the application and enter the following token:')
            ->line("Your password reset token is:")
            ->line(new HtmlString("<strong>" . $this->token . "</strong>"))
            ->line("If you didn't request a password reset, please ignore this message.");
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
