<?php

namespace App\Enums;

enum WorkType: string
{
    case PRELIMINARIES = 'Preliminaries';

    case MAIN_SCOPE = 'Main Scope';

    public static function getOptions(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($case) => [
                $case->value => ucwords(strtolower(str_replace('_', ' ', $case->name))),
            ])
            ->toArray();
    }
}
