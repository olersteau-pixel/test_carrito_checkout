<?php

declare(strict_types=1);

namespace App\Cart\Domain\Exception;

use App\Shared\Domain\Exception\DomainException;
use Symfony\Component\HttpFoundation\Response;

final class CartNotFoundException extends DomainException
{
    public static function withId(string $cartId): self
    {
        return new self(sprintf('El carrito con el id "%s" no existe', $cartId), Response::HTTP_NOT_FOUND);
    }
}
