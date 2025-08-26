<?php

declare(strict_types=1);

namespace App\Cart\Application\Handlers\UpdateCartItem;

use App\Cart\Domain\Exception\CartNotFoundException;
use App\Cart\Domain\Exception\ProductNotFoundException;
use App\Cart\Domain\Interfaces\CartRepositoryInterface;
use App\Cart\Domain\Interfaces\ProductRepositoryInterface;
use App\Cart\Domain\ValueObject\CartId;
use App\Cart\Domain\ValueObject\ProductId;
use App\Shared\Application\Bus\CommandHandlerInterface;

final class UpdateCartItemHandler implements CommandHandlerInterface
{
    public function __construct(
        private CartRepositoryInterface $cartRepository,
        private ProductRepositoryInterface $productRepository,
    ) {
    }

    public function __invoke(UpdateCartItemCommand $command): mixed
    {
        $cartId = new CartId($command->cartId);
        $productId = new ProductId($command->productId);
        $quantity = $command->quantity;

        $cart = $this->cartRepository->findById($cartId);
        if (!$cart) {
            throw CartNotFoundException::withId($command->cartId);
        }

        $product = $this->productRepository->findById($productId);
        if (!$product) {
            throw ProductNotFoundException::withId($command->productId);
        }
        $cart->updateItemQuantity($product, $quantity);
        $this->cartRepository->save($cart);

        return null;        
    }
}
