<?php

declare(strict_types=1);

namespace App\Cart\Application\DTO;

final class CartDTO
{
    public string $id;
    /** @var CartItemDTO[] */
    public array $items;
    public float $totalAmount;
    public string $createdAt;
    public ?string $updatedAt;

    public function __construct(
        string $id,
        array $items,
        float $totalAmount,
        string $createdAt,
        ?string $updatedAt,
    ) {
        $this->id = $id;
        $this->items = $items;
        $this->totalAmount = $totalAmount;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }
}
