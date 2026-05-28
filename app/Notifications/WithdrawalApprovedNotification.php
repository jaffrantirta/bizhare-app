<?php

namespace App\Notifications;

use App\Models\Withdrawal;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WithdrawalApprovedNotification extends Notification implements ShouldQueue
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
            ->subject('Penarikan Disetujui - mybisnis')
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line("Permintaan penarikan sebesar **Rp {$amount}** telah disetujui.")
            ->line("Dana akan segera ditransfer ke rekening **{$this->withdrawal->bank_name} - {$this->withdrawal->account_number}**.")
            ->action('Lihat Status', config('app.url'))
            ->line('Terima kasih telah menggunakan mybisnis!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'          => 'withdrawal_approved',
            'withdrawal_id' => $this->withdrawal->id,
            'amount'        => $this->withdrawal->amount,
            'title'         => 'Penarikan Disetujui',
            'message'       => 'Penarikan sebesar Rp ' . number_format($this->withdrawal->amount, 0, ',', '.') . ' telah disetujui dan sedang diproses.',
        ];
    }
}
