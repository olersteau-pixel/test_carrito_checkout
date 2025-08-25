<?php

declare(strict_types=1);

namespace App\Order\Domain\ValueObject;

use App\Shared\Domain\ValueObject\AbstractId;
use Ramsey\Uuid\Uuid;

final class OrderId extends AbstractId
{
    public static function generate(): self
    {
        return new self(Uuid::uuid4()->toString());
    }
}
