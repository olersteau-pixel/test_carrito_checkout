<?php

declare(strict_types=1);

namespace App\Cart\Domain\Entity;

use App\Cart\Domain\Exception\ProductNotFoundException;

class Cart
{
    private string $id;
    private iterable $items;
    private \DateTimeImmutable $createdAt;
    private ?\DateTimeImmutable $updatedAt;

    public function __construct(string $id)
    {
        $this->id = $id;
        $this->items = [];
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = null;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function items(): array
    {
        return is_array($this->items) ? $this->items : iterator_to_array($this->items);
    }

    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function updatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function addItem(Cart $cart, Product $product, int $quantity): void
    {
        $productId = $product->id();

        if ($this->hasItem($productId)) {
            $this->updateItemQuantity($product, $quantity, true);
        } else {
            $cartItem = new CartItem(
                $cart,
                $productId,
                $product->name(),
                $product->price(),
                $quantity
            );
            $itemsArray = $this->items();
            $itemsArray = $cartItem;
            // @phpstan-ignore-next-line
            $this->items[] = $itemsArray;
        }

        $this->markAsUpdated();
    }

    public function removeItem(Cart $cart, Product $product): void
    {
        $productId = $product->id();
        if (!$this->hasItem($productId)) {
            throw ProductNotFoundException::withId($product->name());
        }

        $itemsArray = $this->items();
        foreach ($itemsArray as $key => $item) {
            if ($item->productId() == $productId) {
                // @phpstan-ignore-next-line
                unset($this->items[$key]);
            }
        }

        $this->markAsUpdated();
    }

    public function updateItemQuantity(Product $product, int $quantity, bool $insert = false): void
    {
        $productId = $product->id();
        if (!$this->hasItem($productId)) {
            throw ProductNotFoundException::withName($product->name());
        }

        foreach ($this->items() as $item) {
            if ($item->productId() == $productId) {
                if ($insert) {
                    $quantity += $item->quantity();
                }
                $item->setQuantity($quantity);
            }
        }
        $this->markAsUpdated();
    }

    public function hasItem(string $productId): bool
    {
        $count = array_filter($this->items(), function ($item) use ($productId) {
            return $item->productId() == $productId;
        });

        return !empty($count);
    }

    public function getItem(string $productId): CartItem
    {
        if (!$this->hasItem($productId)) {
            throw ProductNotFoundException::withId($productId);
        }
        $itemsArray = $this->items();
        $this->items = $itemsArray;
        $result = array_filter($this->items(), function ($item) use ($productId) {
            return $item->productId() == $productId;
        });

        return current($result);
    }

    public function totalAmount(): float
    {
        $total = 0;

        foreach ($this->items as $item) {
            $total += $item->totalPrice();
        }

        return $total;
    }

    public function isEmpty(): bool
    {
        return empty($this->items());
    }

    public function clear(): void
    {
        $this->items = [];
        $this->markAsUpdated();
    }

    private function markAsUpdated(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }
}
