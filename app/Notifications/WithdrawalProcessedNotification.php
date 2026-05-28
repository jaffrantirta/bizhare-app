<?php

namespace App\Notifications;

use App\Models\Withdrawal;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WithdrawalProcessedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Withdrawal $withdrawal) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $amount = number_format($this->withdrawal->amount, 0, ',', '.');

        return (new MailMessage)
            ->subject('Penarikan Berhasil Diproses - mybisnis')
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line("Penarikan sebesar **Rp {$amount}** telah berhasil ditransfer.")
            ->line("Dana telah dikirim ke rekening **{$this->withdrawal->bank_name} - {$this->withdrawal->account_number}** atas nama **{$this->withdrawal->account_name}**.")
            ->line('Mohon cek rekening Anda dalam beberapa saat.')
            ->action('Lihat Riwayat', config('app.url'))
            ->line('Terima kasih telah menggunakan mybisnis!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'           => 'withdrawal_processed',
            'withdrawal_id'  => $this->withdrawal->id,
            'amount'         => $this->withdrawal->amount,
            'bank_name'      => $this->withdrawal->bank_name,
            'account_number' => $this->withdrawal->account_number,
            'title'          => 'Penarikan Berhasil',
            'message'        => 'Penarikan sebesar Rp ' . number_format($this->withdrawal->amount, 0, ',', '.') . ' telah berhasil ditransfer ke rekening Anda.',
        ];
    }
}
