<?php

namespace App\Notifications;

use App\Models\Withdrawal;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WithdrawalRejectedNotification extends Notification implements ShouldQueue
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
            ->subject('Penarikan Ditolak - mybisnis')
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line("Permintaan penarikan sebesar **Rp {$amount}** ditolak.")
            ->when($this->withdrawal->notes, fn ($mail) => $mail->line("Alasan: {$this->withdrawal->notes}"))
            ->line('Dana sebesar **Rp ' . $amount . '** telah dikembalikan ke saldo akun Anda.')
            ->action('Lihat Saldo', config('app.url'))
            ->line('Silakan hubungi support kami jika ada pertanyaan.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'          => 'withdrawal_rejected',
            'withdrawal_id' => $this->withdrawal->id,
            'amount'        => $this->withdrawal->amount,
            'notes'         => $this->withdrawal->notes,
            'title'         => 'Penarikan Ditolak',
            'message'       => 'Penarikan sebesar Rp ' . number_format($this->withdrawal->amount, 0, ',', '.') . ' ditolak. Dana telah dikembalikan ke saldo Anda.',
        ];
    }
}
