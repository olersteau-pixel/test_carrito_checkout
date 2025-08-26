<?php

declare(strict_types=1);

namespace App\Cart\Application\Handlers\UpdateCartItem;

use App\Shared\Application\Bus\CommandInterface;

final class UpdateCartItemCommand implements CommandInterface
{
    public function __construct(
        public readonly string $cartId,
        public readonly string $productId,
        public readonly int $quantity,
    ) {
    }
}
