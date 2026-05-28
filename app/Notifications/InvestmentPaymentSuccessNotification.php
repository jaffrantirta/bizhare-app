<?php

namespace App\Notifications;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvestmentPaymentSuccessNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Transaction $transaction) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $amount    = number_format($this->transaction->amount, 0, ',', '.');
        $typeLabel = $this->transaction->type === 'installment' ? 'Cicilan' : 'Investasi';

        return (new MailMessage)
            ->subject("Pembayaran {$typeLabel} Berhasil - BizShare")
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line("Pembayaran {$typeLabel} sebesar **Rp {$amount}** telah berhasil dikonfirmasi.")
            ->line('Investasi Anda sekarang aktif.')
            ->action('Lihat Portofolio', config('app.url'))
            ->line('Terima kasih telah berinvestasi bersama BizShare!');
    }

    public function toArray(object $notifiable): array
    {
        $typeLabel = $this->transaction->type === 'installment' ? 'Cicilan' : 'Investasi';

        return [
            'type'           => 'investment_payment_success',
            'transaction_id' => $this->transaction->id,
            'payment_type'   => $this->transaction->type,
            'amount'         => $this->transaction->amount,
            'title'          => "Pembayaran {$typeLabel} Berhasil",
            'message'        => "Pembayaran {$typeLabel} sebesar Rp " . number_format($this->transaction->amount, 0, ',', '.') . ' telah dikonfirmasi. Investasi Anda aktif.',
        ];
    }
}
