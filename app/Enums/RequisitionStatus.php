<?php

namespace App\Enums;

enum RequisitionStatus: string
{
    case DRAFT = 'DRAFT';

    case SUBMITTED = 'SUBMITTED';

    case APPROVED = 'APPROVED';

    case REJECTED = 'REJECTED';

    public function getLabel(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::SUBMITTED => 'Submitted',
            self::APPROVED => 'Approved',
            self::REJECTED => 'Rejected',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::DRAFT => 'gray',
            self::SUBMITTED => 'warning',
            self::APPROVED => 'success',
            self::REJECTED => 'danger',
        };
    }
}
