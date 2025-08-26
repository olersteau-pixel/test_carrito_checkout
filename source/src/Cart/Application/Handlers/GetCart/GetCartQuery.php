<?php

declare(strict_types=1);

namespace App\Cart\Application\Handlers\GetCart;

use App\Shared\Application\Bus\QueryInterface;

final class GetCartQuery implements QueryInterface
{
    public function __construct(
        public readonly string $cartId,
    ) {
    }
}
