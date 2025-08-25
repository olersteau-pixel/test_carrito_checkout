<?php

declare(strict_types=1);

namespace App\Order\Domain\Entity;

use App\Cart\Domain\Entity\Cart;
use App\Cart\Domain\ValueObject\ProductId;
use App\Order\Domain\Enum\OrderStatus;
use App\Order\Domain\ValueObject\OrderId;

final class Order
{
    private string $id;
    private string $customerEmail;
    /** @var OrderItem[] */
    private iterable $items;
    private float $totalAmount;
    private string $status;
    private \DateTimeImmutable $createdAt;

    private function __construct(
        string $id,
        string $customerEmail,
        float $totalAmount,
    ) {
        $this->id = $id;
        $this->items = [];
        $this->customerEmail = $customerEmail;
        $this->totalAmount = $totalAmount;
        $this->status = self::getStatus(OrderStatus::PENDING);
        $this->createdAt = new \DateTimeImmutable();
    }

    public static function fromCart(OrderId $id, string $customerEmail, Cart $cart): self
    {
        $items = [];
        $order = new self($id->value(), $customerEmail, $cart->totalAmount());
        foreach ($cart->items() as $cartItem) {
            $productId = new ProductId($cartItem->productId());
            $orderItem = new OrderItem(
                $order,
                $productId,
                $cartItem->productName(),
                $cartItem->unitPrice(),
                $cartItem->quantity()
            );
            $order->setItem($orderItem);
        }

        return $order;
    }

    public function setItem(OrderItem $orderItem): void
    {
        $itemsArray = is_array($this->items) ? $this->items : iterator_to_array($this->items);
        $itemsArray[] = $orderItem;
        $this->items = $itemsArray;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function customerEmail(): string
    {
        return $this->customerEmail;
    }

    public function items(): iterable
    {
        return $this->items;
    }

    public function totalAmount(): float
    {
        return $this->totalAmount;
    }

    public function status(): string
    {
        return $this->status;
    }

    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function confirm(): void
    {
        $this->status = self::getStatus(OrderStatus::CONFIRMED);
    }

    public function cancel(): void
    {
        $this->status = self::getStatus(OrderStatus::CANCELLED);
    }

    private static function getStatus(OrderStatus $status): string
    {
        return $status->value;
    }
}
