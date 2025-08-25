<?php

declare(strict_types=1);

namespace App\Cart\Infrastructure\Controllers;

use App\Cart\Application\DTO\GetCartDTO;
use App\Cart\Application\Handlers\GetCart\GetCartHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

final class GetCartController extends AbstractController
{
    public function __construct(
        private GetCartHandler $getCartHandler,
    ) {
    }

    #[Route('/cart/{cartId}', name: 'cart_get', methods: ['GET'])]
    public function __invoke(string $cartId): JsonResponse
    {
        try {
            $dto = new GetCartDTO($cartId);
            $cartDTO = ($this->getCartHandler)($dto);

            return $this->json([
                'id' => $cartDTO->id,
                'items' => array_map(static fn ($item) => [
                    'product_id' => $item->productId,
                    'product_name' => $item->productName,
                    'unit_price' => $item->unitPrice,
                    'quantity' => $item->quantity,
                    'total_price' => $item->totalPrice,
                ], $cartDTO->items),
                'total_amount' => $cartDTO->totalAmount,
                'created_at' => $cartDTO->createdAt,
                'updated_at' => $cartDTO->updatedAt,
            ]);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], $e->getCode());
        }
    }
}
