<?php

declare(strict_types=1);

namespace App\Cart\Application\Handlers\RemoveItemFromCart;
use App\Shared\Application\Bus\CommandInterface;

final class RemoveItemFromCartCommand implements CommandInterface
{
    public string $cartId;
    public string $productId;

    public function __construct(string $cartId, string $productId)
    {
        $this->cartId = $cartId;
        $this->productId = $productId;
    }
}
