<?php

declare(strict_types=1);

namespace App\Cart\Domain\ValueObject;

use App\Shared\Domain\ValueObject\AbstractId;
use Ramsey\Uuid\Uuid;

final class CartId extends AbstractId
{
    public static function generate(): self
    {
        return new self(Uuid::uuid4()->toString());
    }
}
