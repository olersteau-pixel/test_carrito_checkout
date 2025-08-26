<?php

declare(strict_types=1);

namespace App\Cart\Application\Handlers\ProcessCheckout;

use App\Cart\Domain\Exception\CartNotFoundException;
use App\Cart\Domain\Interfaces\CartRepositoryInterface;
use App\Cart\Domain\ValueObject\CartId;
use App\Order\Domain\Entity\Order;
use App\Order\Domain\Interfaces\OrderRepositoryInterface;
use App\Order\Domain\ValueObject\OrderId;
use App\Shared\Application\Bus\CommandHandlerInterface;

final class ProcessCheckoutHandler implements CommandHandlerInterface
{
    public function __construct(
        private CartRepositoryInterface $cartRepository,
        private OrderRepositoryInterface $orderRepository,
    ) {
    }

    public function __invoke(ProcessCheckoutCommand $command): string
    {
        $cartId = new CartId($command->cartId);
        $cart = $this->cartRepository->findById($cartId);

        if (!$cart) {
            throw CartNotFoundException::withId($command->cartId);
        }

        if ($cart->isEmpty()) {
            throw new \InvalidArgumentException('El carrito esta vacio, no se puede procesar');
        }

        $orderId = OrderId::generate();
        $order = Order::fromCart($orderId, $command->customerEmail, $cart);
        $order->confirm();

        $this->orderRepository->save($order);

        $cart->clear();
        $this->cartRepository->save($cart);

        return $order->id();
    }
}
