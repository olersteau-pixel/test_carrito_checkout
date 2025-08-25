<?php

declare(strict_types=1);

namespace App\Cart\Domain\Entity;

final class CartItem
{
    private int $id;
    private string $productId;
    private string $productName;
    private float $unitPrice;
    private int $quantity;
    private Cart $cart;

    public function __construct(Cart $cart, string $productId, string $productName, float $unitPrice, int $quantity)
    {
        $this->id = 0;
        $this->cart = $cart;
        $this->productId = $productId;
        $this->productName = $productName;
        $this->unitPrice = $unitPrice;
        $this->quantity = $quantity;
    }

    public function id(): int
    {
        return $this->id;
    }

    public function productId(): string
    {
        return $this->productId;
    }

    public function cart(): Cart
    {
        return $this->cart;
    }

    public function productName(): string
    {
        return $this->productName;
    }

    public function unitPrice(): float
    {
        return $this->unitPrice;
    }

    public function quantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function totalPrice(): float
    {
        return $this->unitPrice * $this->quantity;
    }
}
