<?php

namespace App\Enums;

enum RiskLevel: string
{
    case A = 'A';
    case B = 'B';
    case C = 'C';
    case D = 'D';
    case F = 'F';

    public function label(): string
    {
        return match($this) {
            self::A => 'Sûr',
            self::B => 'Faible risque',
            self::C => 'Risque modéré',
            self::D => 'Risque élevé',
            self::F => 'Très exposé',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::A => '#22c55e',
            self::B => '#84cc16',
            self::C => '#f59e0b',
            self::D => '#f97316',
            self::F => '#ef4444',
        };
    }

    public static function fromScore(int $score): self
    {
        return match(true) {
            $score === 0          => self::A,
            $score <= 2           => self::B,
            $score <= 5           => self::C,
            $score <= 10          => self::D,
            default               => self::F,
        };
    }
}
