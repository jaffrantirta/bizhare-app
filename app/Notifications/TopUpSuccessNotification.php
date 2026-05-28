<?php

namespace App\Notifications;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TopUpSuccessNotification extends Notification implements ShouldQueue
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
            ->subject('Top Up Berhasil - BizShare')
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line("Top up sebesar **Rp {$amount}** telah berhasil dikonfirmasi.")
            ->line('Akun Anda sekarang aktif dan siap untuk berinvestasi.')
            ->action('Mulai Investasi', config('app.url'))
            ->line('Terima kasih telah bergabung dengan BizShare!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'           => 'topup_success',
            'transaction_id' => $this->transaction->id,
            'amount'         => $this->transaction->amount,
            'title'          => 'Top Up Berhasil',
            'message'        => 'Top up sebesar Rp ' . number_format($this->transaction->amount, 0, ',', '.') . ' telah dikonfirmasi.',
        ];
    }
}
