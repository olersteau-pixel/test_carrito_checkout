<?php

declare(strict_types=1);

namespace App\Cart\Application\Handlers\AddItemToCart;
use App\Shared\Application\Bus\CommandInterface;

final class AddItemToCartCommand implements CommandInterface
{
    public function __construct(
        public readonly string $cartId,
        public readonly string $productId,
        public readonly int $quantity
    ) {}
}
