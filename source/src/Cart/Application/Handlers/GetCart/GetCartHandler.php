<?php

declare(strict_types=1);

namespace App\Cart\Application\Handlers\GetCart;

use App\Cart\Application\DTO\CartDTO;
use App\Cart\Application\DTO\CartItemDTO;
use App\Cart\Application\DTO\GetCartDTO;
use App\Cart\Domain\Exception\CartNotFoundException;
use App\Cart\Domain\Interfaces\CartRepositoryInterface;
use App\Cart\Domain\ValueObject\CartId;

final class GetCartHandler
{
    public function __construct(
        private CartRepositoryInterface $cartRepository,
    ) {
    }

    public function __invoke(GetCartDTO $dto): CartDTO
    {
        $cartId = new CartId($dto->cartId);
        $cart = $this->cartRepository->findById($cartId);

        if (!$cart) {
            throw CartNotFoundException::withId($dto->cartId);
        }

        $items = [];
        foreach ($cart->items() as $item) {
            $items[] = new CartItemDTO(
                $item->productId(),
                $item->productName(),
                $item->unitPrice(),
                $item->quantity(),
                $item->totalPrice()
            );
        }

        return new CartDTO(
            $cart->id(),
            $items,
            $cart->totalAmount(),
            $cart->createdAt()->format('Y-m-d H:i:s'),
            $cart->updatedAt()?->format('Y-m-d H:i:s')
        );
    }
}
