<?php

declare(strict_types=1);

namespace App\Cart\Application\DTO;

final class RemoveItemFromCartDTO
{
    public string $cartId;
    public string $productId;

    public function __construct(string $cartId, string $productId)
    {
        $this->cartId = $cartId;
        $this->productId = $productId;
    }
}
