<?php

declare(strict_types=1);

namespace App\Cart\Application\DTO;

final class CartItemDTO
{
    public string $productId;
    public string $productName;
    public float $unitPrice;
    public int $quantity;
    public float $totalPrice;

    public function __construct(
        string $productId,
        string $productName,
        float $unitPrice,
        int $quantity,
        float $totalPrice,
    ) {
        $this->productId = $productId;
        $this->productName = $productName;
        $this->unitPrice = $unitPrice;
        $this->quantity = $quantity;
        $this->totalPrice = $totalPrice;
    }
}
