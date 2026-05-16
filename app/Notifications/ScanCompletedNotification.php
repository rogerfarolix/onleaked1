<?php

namespace App\Notifications;

use App\Models\Report;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ScanCompletedNotification extends Notification
{
    public function __construct(
        private readonly Report $report,
        private readonly string $recipientEmail,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $scan      = $this->report->scan;
        $riskLabel = $this->report->riskLabel();
        $riskColor = $this->report->riskColor();

        return (new MailMessage)
            ->subject('🔍 Votre rapport Onleaked est prêt')
            ->greeting('Bonjour,')
            ->line("Votre analyse pour **{$scan->email_target}** est terminée.")
            ->line("📊 **Comptes détectés :** {$this->report->accounts_found}")
            ->line("💥 **Fuites détectées :** {$this->report->breaches_found}")
            ->line("⚠️ **Niveau de risque :** {$riskLabel}")
            ->action('Voir le rapport complet', route('scans.show', $scan->uuid))
            ->line('Ce rapport est fourni à titre informatif pour vous aider à protéger votre identité numérique.')
            ->salutation('— L\'équipe Onleaked · onleaked.nealix.org');
    }
}
