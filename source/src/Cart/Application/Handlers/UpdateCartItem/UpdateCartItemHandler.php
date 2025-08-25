<?php

declare(strict_types=1);

namespace App\Cart\Application\Handlers\UpdateCartItem;

use App\Cart\Application\DTO\UpdateCartItemDTO;
use App\Cart\Domain\Exception\CartNotFoundException;
use App\Cart\Domain\Exception\ProductNotFoundException;
use App\Cart\Domain\Interfaces\CartRepositoryInterface;
use App\Cart\Domain\Interfaces\ProductRepositoryInterface;
use App\Cart\Domain\ValueObject\CartId;
use App\Cart\Domain\ValueObject\ProductId;

final class UpdateCartItemHandler
{
    public function __construct(
        private CartRepositoryInterface $cartRepository,
        private ProductRepositoryInterface $productRepository,
    ) {
    }

    public function __invoke(UpdateCartItemDTO $dto): void
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
        $cart->updateItemQuantity($product, $quantity);
        $this->cartRepository->save($cart);
    }
}
