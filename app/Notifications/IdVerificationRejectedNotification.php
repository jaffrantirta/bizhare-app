<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class IdVerificationRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly ?string $reason = null) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Verifikasi Identitas Ditolak - mybisnis')
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line('Maaf, verifikasi identitas Anda tidak dapat disetujui.');

        if ($this->reason) {
            $mail->line("**Alasan:** {$this->reason}");
        }

        return $mail
            ->line('Silakan unggah ulang dokumen identitas Anda yang valid dan pastikan foto terlihat jelas.')
            ->action('Unggah Ulang Dokumen', config('app.url'))
            ->line('Jika ada pertanyaan, silakan hubungi tim support kami.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'    => 'id_verification_rejected',
            'title'   => 'Verifikasi Identitas Ditolak',
            'message' => $this->reason
                ? "Verifikasi ditolak: {$this->reason}"
                : 'Verifikasi identitas Anda ditolak. Silakan unggah ulang dokumen Anda.',
            'reason'  => $this->reason,
        ];
    }
}
