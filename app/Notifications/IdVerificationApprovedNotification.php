<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class IdVerificationApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Verifikasi Identitas Disetujui - mybisnis')
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line('Selamat! Identitas Anda telah berhasil diverifikasi.')
            ->line('Akun Anda sekarang aktif dan Anda dapat mulai berinvestasi di mybisnis.')
            ->action('Mulai Investasi', config('app.url'))
            ->line('Terima kasih telah bergabung dengan mybisnis!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'    => 'id_verification_approved',
            'title'   => 'Verifikasi Identitas Disetujui',
            'message' => 'Identitas Anda telah diverifikasi. Akun Anda sekarang aktif.',
        ];
    }
}
