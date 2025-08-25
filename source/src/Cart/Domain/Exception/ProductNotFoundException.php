<?php

declare(strict_types=1);

namespace App\Cart\Domain\Exception;

use App\Shared\Domain\Exception\DomainException;
use Symfony\Component\HttpFoundation\Response;

final class ProductNotFoundException extends DomainException
{
    public static function withId(string $productId): self
    {
        return new self(sprintf('El producto con el id "%s" no existe', $productId), Response::HTTP_NOT_FOUND);
    }

    public static function withName(string $productName): self
    {
        return new self(sprintf('El producto "%s" no existe', $productName), Response::HTTP_NOT_FOUND);
    }
}
