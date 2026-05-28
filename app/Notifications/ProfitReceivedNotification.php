<?php

namespace App\Notifications;

use App\Models\Business;
use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProfitReceivedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Transaction $transaction,
        public readonly Business $business,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $amount = number_format($this->transaction->amount, 0, ',', '.');

        return (new MailMessage)
            ->subject('Bagi Hasil Diterima - mybisnis')
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line("Selamat! Anda menerima bagi hasil sebesar **Rp {$amount}** dari bisnis **{$this->business->name}**.")
            ->line('Dana telah ditambahkan ke saldo akun Anda.')
            ->action('Lihat Portofolio', config('app.url'))
            ->line('Terima kasih telah berinvestasi bersama mybisnis!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'           => 'profit_received',
            'transaction_id' => $this->transaction->id,
            'amount'         => $this->transaction->amount,
            'business_id'    => $this->business->id,
            'business_name'  => $this->business->name,
            'title'          => 'Bagi Hasil Diterima',
            'message'        => 'Anda menerima bagi hasil Rp ' . number_format($this->transaction->amount, 0, ',', '.') . " dari bisnis {$this->business->name}.",
        ];
    }
}
