<?php

declare(strict_types=1);

namespace App\Cart\Application\Handlers\RemoveItemFromCart;

use App\Cart\Domain\Exception\CartNotFoundException;
use App\Cart\Domain\Exception\ProductNotFoundException;
use App\Cart\Domain\Interfaces\CartRepositoryInterface;
use App\Cart\Domain\Interfaces\ProductRepositoryInterface;
use App\Cart\Domain\ValueObject\CartId;
use App\Cart\Domain\ValueObject\ProductId;
use App\Shared\Application\Bus\CommandHandlerInterface;


final class RemoveItemFromCartHandler implements CommandHandlerInterface
{
    public function __construct(
        private CartRepositoryInterface $cartRepository,
        private ProductRepositoryInterface $productRepository,
    ) {
    }

    public function __invoke(RemoveItemFromCartCommand $command): void
    {
        $cartId = new CartId($command->cartId);
        $productId = new ProductId($command->productId);

        $cart = $this->cartRepository->findById($cartId);
        if (!$cart) {
            throw CartNotFoundException::withId($command->cartId);
        }

        $product = $this->productRepository->findById($productId);
        if (!$product) {
            throw ProductNotFoundException::withId($command->productId);
        }

        $cart->removeItem($cart, $product);
        $this->cartRepository->save($cart);
    }
}
