<?php

namespace App\Notifications;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TopUpFailedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Transaction $transaction) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $amount = number_format($this->transaction->amount, 0, ',', '.');

        return (new MailMessage)
            ->subject('Top Up Gagal - mybisnis')
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line("Top up sebesar **Rp {$amount}** tidak dapat dikonfirmasi.")
            ->line('Silakan hubungi tim support kami jika Anda sudah melakukan pembayaran.')
            ->action('Hubungi Support', config('app.url'))
            ->line('Terima kasih atas perhatian Anda.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'           => 'topup_failed',
            'transaction_id' => $this->transaction->id,
            'amount'         => $this->transaction->amount,
            'title'          => 'Top Up Gagal',
            'message'        => 'Top up sebesar Rp ' . number_format($this->transaction->amount, 0, ',', '.') . ' tidak dapat dikonfirmasi.',
        ];
    }
}
