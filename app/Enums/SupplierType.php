<?php

namespace App\Enums;

enum SupplierType: string
{
    case MATERIAL = 'material';

    case SUBCON = 'sub_contractor';

    public static function getOptions(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($case) => [
                $case->value => ucwords(strtolower(str_replace('_', ' ', $case->name))),
            ])
            ->toArray();
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::MATERIAL => 'Material',
            self::SUBCON => 'Sub Contractor',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::MATERIAL => 'gray',
            self::SUBCON => 'warning',
        };
    }
}
