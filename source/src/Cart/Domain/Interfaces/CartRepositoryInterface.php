<?php

declare(strict_types=1);

namespace App\Cart\Domain\Interfaces;

use App\Cart\Domain\Entity\Cart;
use App\Cart\Domain\ValueObject\CartId;

interface CartRepositoryInterface
{
    public function save(Cart $cart): void;

    public function findById(CartId $cartId): ?Cart;

    public function getById(CartId $cartId): Cart;

    public function delete(CartId $cartId): void;

    public function deleteAll(): void;
}
