<?php

namespace App\Enums;

enum PizzaOrderStatus: string
{
    case RECEIVED = 'received';
    case WORKING = 'working';
    case IN_OVEN = 'in_oven';
    case READY = 'ready';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::RECEIVED => 'Received',
            self::WORKING => 'Working',
            self::IN_OVEN => 'In Oven',
            self::READY => 'Ready',
        };
    }
}
