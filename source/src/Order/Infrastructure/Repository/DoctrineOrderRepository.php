<?php

declare(strict_types=1);

namespace App\Order\Infrastructure\Repository;

use App\Order\Domain\Entity\Order;
use App\Order\Domain\Interfaces\OrderRepositoryInterface;
use App\Order\Domain\ValueObject\OrderId;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class DoctrineOrderRepository extends ServiceEntityRepository implements OrderRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function save(Order $order): void
    {
        $this->getEntityManager()->persist($order);
        $this->getEntityManager()->flush();
    }

    public function findById(OrderId $orderId): ?Order
    {
        $order = $this->find($orderId->value());
        assert($order instanceof Order || null === $order);

        return $order;
    }

    public function getById(OrderId $orderId): Order
    {
        $order = $this->findById($orderId);

        if (!$order) {
            throw new \RuntimeException('Pedido no encontrqdo: '.$orderId->value());
        }

        return $order;
    }

    public function findByCustomerEmail(string $email): array
    {
        return $this->createQueryBuilder('o')
            ->where('o.customerEmail = :email')
            ->setParameter('email', $email)
            ->orderBy('o.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
