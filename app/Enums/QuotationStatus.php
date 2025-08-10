<?php

namespace App\Enums;

enum QuotationStatus: string
{
    case DRAFT = 'DRAFT';

    case PENDING = 'PENDING';

    case APPROVED = 'APPROVED';

    case DECLINED = 'DECLINED';

    case REJECTED = 'REJECTED';

    public function getLabel(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::PENDING => 'Pending',
            self::APPROVED => 'Approved',
            self::DECLINED => 'Declined',
            self::REJECTED => 'Rejected',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::DRAFT => 'gray',
            self::PENDING => 'warning',
            self::APPROVED => 'success',
            self::DECLINED => 'danger',
            self::REJECTED => 'danger',
        };
    }
}
