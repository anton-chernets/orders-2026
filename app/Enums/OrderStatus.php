<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Shipped = 'shipped';
    case Delivered = 'delivered';

    public static function transitions(): array
    {
        return [
            self::Pending->value => [self::Confirmed->value],
            self::Confirmed->value => [self::Shipped->value],
            self::Shipped->value => [self::Delivered->value],
            self::Delivered->value => [],
        ];
    }

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Confirmed => 'Confirmed',
            self::Shipped => 'Shipped',
            self::Delivered => 'Delivered',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'gray',
            self::Confirmed => 'info',
            self::Shipped => 'primary',
            self::Delivered => 'success',
        };
    }

    public function canTransitionTo(self $status): bool
    {
        return in_array($status->value, self::transitions()[$this->value]);
    }
}
