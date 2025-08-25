<?php

declare(strict_types=1);

namespace App\Cart\Infrastructure\Repository;

use App\Cart\Domain\Entity\Cart;
use App\Cart\Domain\Exception\CartNotFoundException;
use App\Cart\Domain\Interfaces\CartRepositoryInterface;
use App\Cart\Domain\ValueObject\CartId;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class DoctrineCartRepository extends ServiceEntityRepository implements CartRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cart::class);
    }

    public function save(Cart $cart): void
    {
        $this->getEntityManager()->persist($cart);
        $this->getEntityManager()->flush();
    }

    public function findById(CartId $cartId): ?Cart
    {
        $cart = $this->find($cartId->value());
        assert($cart instanceof Cart || null === $cart);

        return $cart;
    }

    public function getById(CartId $cartId): Cart
    {
        $cart = $this->findById($cartId);

        if (!$cart) {
            throw CartNotFoundException::withId($cartId->value());
        }

        return $cart;
    }

    public function delete(CartId $cartId): void
    {
        $cart = $this->getById($cartId);
        $this->getEntityManager()->remove($cart);
        $this->getEntityManager()->flush();
    }

    public function deleteAll(): void
    {
        $this->createQueryBuilder('e')
            ->delete()
            ->getQuery()
            ->execute();
    }
}
