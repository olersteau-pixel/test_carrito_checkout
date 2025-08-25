<?php

declare(strict_types=1);

namespace App\Order\Domain\Entity;

use App\Cart\Domain\ValueObject\ProductId;

final class OrderItem
{
    private int $id;
    private string $productId;
    private string $productName;
    private float $unitPrice;
    private int $quantity;
    private Order $order;

    public function __construct(Order $order, ProductId $productId, string $productName, float $unitPrice, int $quantity)
    {
        $this->id = 0;
        $this->order = $order;
        $this->productId = $productId->value();
        $this->productName = $productName;
        $this->unitPrice = $unitPrice;
        $this->quantity = $quantity;
    }

    public function id(): int
    {
        return $this->id;
    }

    public function order(): Order
    {
        return $this->order;
    }

    public function productId(): string
    {
        return $this->productId;
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

    public function totalPrice(): float
    {
        return $this->unitPrice * $this->quantity;
    }
}
