<?php

declare(strict_types=1);

namespace App\Order\Domain\Interfaces;

use App\Order\Domain\Entity\Order;
use App\Order\Domain\ValueObject\OrderId;

interface OrderRepositoryInterface
{
    public function save(Order $order): void;

    public function findById(OrderId $orderId): ?Order;

    public function getById(OrderId $orderId): Order;

    /**
     * @return Order[]
     */
    public function findByCustomerEmail(string $email): array;
}
