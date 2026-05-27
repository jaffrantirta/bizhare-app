<?php

namespace App\Notifications;

use App\Models\InstallmentPayment;
use App\Models\Investment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InstallmentReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly InstallmentPayment $installment,
        public readonly Investment $investment
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $businessName = $this->investment->business->name ?? 'your business';
        $dueDate = $this->installment->due_date->format('d F Y');
        $amount = number_format($this->installment->amount + $this->installment->admin_fee, 0, ',', '.');

        return (new MailMessage)
            ->subject('Pengingat Cicilan BizShare - Jatuh Tempo ' . $dueDate)
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line("Cicilan investasi Anda untuk bisnis **{$businessName}** akan jatuh tempo pada **{$dueDate}**.")
            ->line("Jumlah yang harus dibayar: **Rp {$amount}**")
            ->line("Bulan cicilan: {$this->installment->month_number} dari {$this->investment->tenure_months}")
            ->action('Bayar Sekarang', config('app.url'))
            ->line('Pastikan pembayaran dilakukan sebelum tanggal jatuh tempo.')
            ->line('Terima kasih telah berinvestasi bersama BizShare!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'installment_reminder',
            'investment_id' => $this->investment->id,
            'installment_id' => $this->installment->id,
            'month_number' => $this->installment->month_number,
            'amount' => $this->installment->amount,
            'admin_fee' => $this->installment->admin_fee,
            'due_date' => $this->installment->due_date->format('Y-m-d'),
            'business_name' => $this->investment->business->name ?? null,
        ];
    }
}
