<?php

namespace App\Enums;

enum ScanStatus: string
{
    case Pending   = 'pending';
    case Running   = 'running';
    case Completed = 'completed';
    case Failed    = 'failed';
    case Cached    = 'cached';

    public function label(): string
    {
        return match($this) {
            self::Pending   => 'En attente',
            self::Running   => 'En cours',
            self::Completed => 'Terminé',
            self::Failed    => 'Échoué',
            self::Cached    => 'Depuis le cache',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Pending   => 'yellow',
            self::Running   => 'blue',
            self::Completed => 'green',
            self::Failed    => 'red',
            self::Cached    => 'purple',
        };
    }
}
