<?php

declare(strict_types=1);

namespace App\Cart\Infrastructure\Repository;

use App\Cart\Domain\Entity\Product;
use App\Cart\Domain\Exception\ProductNotFoundException;
use App\Cart\Domain\Interfaces\ProductRepositoryInterface;
use App\Cart\Domain\ValueObject\ProductId;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class DoctrineProductRepository extends ServiceEntityRepository implements ProductRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function save(Product $product): void
    {
        $this->getEntityManager()->persist($product);
        $this->getEntityManager()->flush();
    }

    public function findById(ProductId $productId): ?Product
    {
        $product = $this->find($productId->value());
        assert($product instanceof Product || null === $product);

        return $product;
    }

    public function getById(ProductId $productId): Product
    {
        $product = $this->findById($productId);

        if (!$product) {
            throw ProductNotFoundException::withId($productId->value());
        }

        return $product;
    }

    public function findAll(): array
    {
        return $this->createQueryBuilder('p')
            ->getQuery()
            ->getResult();
    }

    public function deleteAll(): void
    {
        $this->createQueryBuilder('e')
            ->delete()
            ->getQuery()
            ->execute();
    }
}
