<?php

declare(strict_types=1);

namespace App\Cart\Application\Handlers\RemoveItemFromCart;
use App\Shared\Application\Bus\CommandInterface;

final class RemoveItemFromCartCommand implements CommandInterface
{
    public function __construct(
        public readonly string $cartId,
        public readonly string $productId
    ) {}    
}
