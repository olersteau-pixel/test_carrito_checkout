<?php

declare(strict_types=1);

namespace App\Cart\Application\Handlers\ProcessCheckout;

use App\Cart\Application\DTO\ProcessCheckoutDTO;
use App\Cart\Domain\Exception\CartNotFoundException;
use App\Cart\Domain\Interfaces\CartRepositoryInterface;
use App\Cart\Domain\ValueObject\CartId;
use App\Order\Domain\Entity\Order;
use App\Order\Domain\Interfaces\OrderRepositoryInterface;
use App\Order\Domain\ValueObject\OrderId;

final class ProcessCheckoutHandler
{
    public function __construct(
        private CartRepositoryInterface $cartRepository,
        private OrderRepositoryInterface $orderRepository,
    ) {
    }

    public function __invoke(ProcessCheckoutDTO $dto): string
    {
        $cartId = new CartId($dto->cartId);
        $cart = $this->cartRepository->findById($cartId);

        if (!$cart) {
            throw CartNotFoundException::withId($dto->cartId);
        }

        if ($cart->isEmpty()) {
            throw new \InvalidArgumentException('El carrito esta vacio, no se puede procesar');
        }

        $orderId = OrderId::generate();
        $order = Order::fromCart($orderId, $dto->customerEmail, $cart);
        $order->confirm();

        $this->orderRepository->save($order);

        $cart->clear();
        $this->cartRepository->save($cart);

        return $order->id();
    }
}
