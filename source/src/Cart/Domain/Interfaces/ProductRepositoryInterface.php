<?php

declare(strict_types=1);

namespace App\Cart\Domain\Interfaces;

use App\Cart\Domain\Entity\Product;
use App\Cart\Domain\ValueObject\ProductId;

interface ProductRepositoryInterface
{
    public function save(Product $product): void;

    public function findById(ProductId $productId): ?Product;

    public function getById(ProductId $productId): Product;

    public function findAll(): array;

    public function deleteAll(): void;
}
