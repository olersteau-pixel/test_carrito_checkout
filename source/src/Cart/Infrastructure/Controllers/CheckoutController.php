<?php

declare(strict_types=1);

namespace App\Cart\Infrastructure\Controllers;

use App\Cart\Application\DTO\ProcessCheckoutDTO;
use App\Cart\Application\Handlers\ProcessCheckout\ProcessCheckoutHandler;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CheckoutController extends AbstractController
{
    public function __construct(
        private ProcessCheckoutHandler $checkoutHandler,
    ) {
    }

    #[Route('/cart/{cartId}/checkout', name: 'cart_checkout', methods: ['POST'])]
    #[OA\Post(
        path: '/api/cart/{cartId}/checkout',
        summary: 'Procesa el carrito y genera un pedido',
        parameters: [
            new OA\Parameter(
                name: 'cartId',
                in: 'path',
                description: 'ID del carrito',
                required: true,
                schema: new OA\Schema(type: 'string', format: 'uuid')
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: 'object',
                properties: [
                    new OA\Property(property: 'customer_email', type: 'string', description: 'Email del cliente'),
                ],
                required: ['customer_email']
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Checkout Realizado correcto se ha generado el id de pedido 6222b469-a0e6-4b3d-9a30-5e2b809b9d41'),
            new OA\Response(response: 400,
                description: 'Error en los datos',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'error', type: 'string'),
                        new OA\Property(property: 'code', type: 'integer'),
                    ],
                    examples: [
                        new OA\Examples(
                            'Falta el campo de cantidad',
                            'Falta el campo de cantidad',
                            null,
                            ['error' => 'Falta el campo de cantidad', 'code' => 404]
                        ),
                        new OA\Examples(
                            'Carrito sin producto',
                            'Carrito sin producto',
                            null,
                            ['error' => 'El carrito esta vacio, no se puede procesar', 'code' => 404]
                        ),
                    ]
                )
            ),
            new OA\Response(response: 404,
                description: 'Recurso no encontrado',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'error', type: 'string'),
                        new OA\Property(property: 'code', type: 'integer'),
                    ],
                    examples: [
                        new OA\Examples(
                            'Carrito no encontrado',
                            'Carrito no existe',
                            null,
                            ['error' => 'El carrito con el id "6222b469-a0e6-4b3d-9a30-5e2b809b9d41" no existe', 'code' => 404]
                        ),
                        new OA\Examples(
                            'Producto no encontrado',
                            'Producto no está en el carrito',
                            null,
                            ['error' => 'El producto con el id "3fa85f64-5717-4562-b3fc-2c963f66afa6" no existe', 'code' => 404]
                        ),
                    ]
                )
            ),
        ],
        tags: ['Carrito']
    )]
    public function __invoke(Request $request, string $cartId): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            if (!isset($data['customer_email'])) {
                return $this->json(['error' => 'Falta el campo de cantidad'], Response::HTTP_BAD_REQUEST);
            }

            $dto = new ProcessCheckoutDTO(
                $cartId,
                $data['customer_email']
            );

            $orderId = ($this->checkoutHandler)($dto);

            return $this->json([
                'message' => sprintf('Checkout Realizado correcto se ha generado el id de pedido %s', $orderId),
            ], Response::HTTP_CREATED);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], $e->getCode());
        }
    }
}
