<?php

namespace App\Notifications;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReferralRewardNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Transaction $transaction,
        public readonly User $newUser,
        public readonly int $level,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $amount = number_format($this->transaction->amount, 0, ',', '.');

        return (new MailMessage)
            ->subject('Bonus Referral Diterima - mybisnis')
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line("Selamat! Anda mendapatkan bonus referral sebesar **Rp {$amount}**.")
            ->line("**{$this->newUser->name}** (level {$this->level} dari jaringan Anda) telah menyelesaikan setoran awal.")
            ->line('Bonus telah ditambahkan ke saldo akun Anda.')
            ->action('Lihat Saldo', config('app.url'))
            ->line('Terus ajak teman untuk mendapatkan lebih banyak bonus!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'           => 'referral_reward',
            'transaction_id' => $this->transaction->id,
            'amount'         => $this->transaction->amount,
            'new_user_name'  => $this->newUser->name,
            'level'          => $this->level,
            'title'          => 'Bonus Referral Diterima',
            'message'        => 'Anda mendapat bonus referral Rp ' . number_format($this->transaction->amount, 0, ',', '.') . " dari {$this->newUser->name} (Level {$this->level}).",
        ];
    }
}
