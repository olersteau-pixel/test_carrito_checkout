<?php

declare(strict_types=1);

namespace App\Cart\Infrastructure\Http\Controllers;

use App\Cart\Application\Handlers\GetCart\GetCartQuery;
use App\Cart\Infrastructure\Http\Requests\GetCartRequest;
use App\Shared\Application\Bus\QueryBusInterface;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class GetCartController extends AbstractController
{
    public function __construct(
        private QueryBusInterface $queryBus,
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
    public function __invoke(string $cartId,
        DenormalizerInterface $serializer,
        ValidatorInterface $validator): JsonResponse
    {
        try {
            $addItemRequest = $serializer->denormalize(['cart_id' => $cartId], GetCartRequest::class);
            $errors = $validator->validate($addItemRequest);

            if (count($errors) > 0) {
                $errorMessages = [];
                foreach ($errors as $error) {
                    $errorMessages[] = $error->getMessage();
                }

                return $this->json(['error' => implode(',', $errorMessages)], Response::HTTP_BAD_REQUEST);
            }

            $query = new GetCartQuery($cartId);
            $cartDTO = $this->queryBus->handle($query);

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
