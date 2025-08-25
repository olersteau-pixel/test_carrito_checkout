<?php

declare(strict_types=1);

namespace App\Cart\Application\Handlers\AddItemToCart;

use App\Cart\Application\DTO\AddItemToCartDTO;
use App\Cart\Domain\Exception\CartNotFoundException;
use App\Cart\Domain\Exception\ProductNotFoundException;
use App\Cart\Domain\Interfaces\CartRepositoryInterface;
use App\Cart\Domain\Interfaces\ProductRepositoryInterface;
use App\Cart\Domain\ValueObject\CartId;
use App\Cart\Domain\ValueObject\ProductId;

final class AddItemToCartHandler
{
    public function __construct(
        private CartRepositoryInterface $cartRepository,
        private ProductRepositoryInterface $productRepository,
    ) {
    }

    public function __invoke(AddItemToCartDTO $dto): void
    {
        $cartId = new CartId($dto->cartId);
        $productId = new ProductId($dto->productId);
        $quantity = $dto->quantity;

        $cart = $this->cartRepository->findById($cartId);
        if (!$cart) {
            throw CartNotFoundException::withId($dto->cartId);
        }
        $product = $this->productRepository->findById($productId);

        if (!$product) {
            throw ProductNotFoundException::withId($dto->productId);
        }

        $cart->addItem($cart, $product, $quantity);
        $this->cartRepository->save($cart);
    }
}
