<?php

namespace App\Notifications\Auth;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use SensitiveParameter;

class LoginOtpNotification extends Notification
{
    public function __construct(
        #[SensitiveParameter]
        public string $code,
        public int $codeExpiryMinutes,
    ) {}

    /**
     * @return array<string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your ART Story login OTP')
            ->greeting('Hello ' . ($notifiable->name ?? 'there') . ',')
            ->line('Use this OTP to complete your dashboard login:')
            ->line($this->code)
            ->line("This code will expire in {$this->codeExpiryMinutes} minutes.")
            ->line('If you did not try to log in, please contact the administrator immediately.');
    }
}
