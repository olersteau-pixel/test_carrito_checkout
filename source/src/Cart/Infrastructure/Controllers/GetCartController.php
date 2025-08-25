<?php

declare(strict_types=1);

namespace App\Cart\Infrastructure\Controllers;

use App\Cart\Application\DTO\GetCartDTO;
use App\Cart\Application\Handlers\GetCart\GetCartHandler;
use OpenApi\Attributes as OA;
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
    #[OA\Get(
        path: '/api/cart/{cartId}',
        summary: 'Obtiene el carrito por ID',
        parameters: [
            new OA\Parameter(
                name: 'cartId',
                in: 'path',
                description: 'ID del carrito',
                required: true,
                schema: new OA\Schema(type: 'string', format: 'uuid')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Carrito encontrado',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'id', type: 'string', format: 'uuid'),
                        new OA\Property(property: 'totalAmount', type: 'number', format: 'float'),
                        new OA\Property(
                            property: 'items',
                            type: 'array',
                            items: new OA\Items(
                                type: 'object',
                                properties: [
                                    new OA\Property(property: 'productId', type: 'string'),
                                    new OA\Property(property: 'productName', type: 'string'),
                                    new OA\Property(property: 'unitPrice', type: 'number', format: 'float'),
                                    new OA\Property(property: 'quantity', type: 'integer'),
                                ]
                            )
                        ),
                    ]
                )
            ),
            new OA\Response(response: 404, description: 'Carrito no encontrado'),
        ],
        tags: ['Carrito']
    )]
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
