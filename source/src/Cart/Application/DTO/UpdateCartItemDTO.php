<?php

declare(strict_types=1);

namespace App\Cart\Application\DTO;

final class UpdateCartItemDTO
{
    public string $cartId;
    public string $productId;
    public int $quantity;

    public function __construct(string $cartId, string $productId, int $quantity)
    {
        $this->cartId = $cartId;
        $this->productId = $productId;
        $this->quantity = $quantity;
    }
}
