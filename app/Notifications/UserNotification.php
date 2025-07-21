<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class UserNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    protected $message;
    protected $data;

    public function __construct(string $message, array $data = [])
    {
        $this->message = $message;
        $this->data    = $data;
    }

    // Channel: lưu vào DB và broadcast real-time
    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    // Dữ liệu lưu vào bảng notifications
    public function toDatabase($notifiable)
    {
        return [
            'message' => $this->message,
            'data'    => $this->data,
        ];
    }

    // Dữ liệu broadcast qua WebSocket
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'message' => $this->message,
            'data'    => $this->data,
            'id'      => $this->id,              // notification id
            'created' => now()->toDateTimeString(),
        ]);
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
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
