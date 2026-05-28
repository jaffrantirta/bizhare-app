<?php

namespace App\Notifications;

use App\Models\Withdrawal;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WithdrawalSubmittedNotification extends Notification implements ShouldQueue
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
            ->subject('Permintaan Penarikan Diterima - mybisnis')
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line("Permintaan penarikan sebesar **Rp {$amount}** telah diterima.")
            ->line("Dana akan ditransfer ke rekening **{$this->withdrawal->bank_name} - {$this->withdrawal->account_number}** atas nama **{$this->withdrawal->account_name}**.")
            ->line('Tim kami akan memproses permintaan Anda dalam 1-3 hari kerja.')
            ->action('Lihat Status Penarikan', config('app.url'))
            ->line('Terima kasih telah menggunakan mybisnis!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'           => 'withdrawal_submitted',
            'withdrawal_id'  => $this->withdrawal->id,
            'amount'         => $this->withdrawal->amount,
            'bank_name'      => $this->withdrawal->bank_name,
            'account_number' => $this->withdrawal->account_number,
            'title'          => 'Permintaan Penarikan Diterima',
            'message'        => 'Permintaan penarikan sebesar Rp ' . number_format($this->withdrawal->amount, 0, ',', '.') . ' sedang diproses.',
        ];
    }
}
