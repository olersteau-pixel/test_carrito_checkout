<?php

declare(strict_types=1);

namespace App\Cart\Infrastructure\Http\Controllers;

use App\Cart\Application\Handlers\AddItemToCart\AddItemToCartCommand;
use App\Cart\Infrastructure\Http\Requests\AddItemRequest;
use App\Shared\Application\Bus\CommandBusInterface;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class AddItemCartController extends AbstractController
{
    public function __construct(
        private CommandBusInterface $commandBus,
    ) {
    }

    #[Route('/cart/{cartId}/items', name: 'cart_add_item', methods: ['POST'])]
    #[OA\Post(
        path: '/api/cart/{cartId}/items',
        summary: 'Actualiza la cantidad de un producto',
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
                    new OA\Property(property: 'product_id', type: 'string', format: 'uuid', description: 'id del producto'),
                    new OA\Property(property: 'quantity', type: 'integer', description: 'Cantidad del producto'),
                ],
                required: ['quantity', 'product_id']
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Carrito actualizado'),
            new OA\Response(response: 400, description: 'Falta el campo de cantidad'),
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
    public function __invoke(Request $request, string $cartId,
        DenormalizerInterface $serializer,
        ValidatorInterface $validator): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $addItemRequest = $serializer->denormalize(['cart_id' => $cartId, ...$data], AddItemRequest::class);
            $errors = $validator->validate($addItemRequest);

            if (count($errors) > 0) {
                $errorMessages = [];
                foreach ($errors as $error) {
                    $errorMessages[] = $error->getMessage();
                }

                return $this->json(['error' => implode(',', $errorMessages)], Response::HTTP_BAD_REQUEST);
            }

            $command = new AddItemToCartCommand(
                $cartId,
                $data['product_id'],
                (int) $data['quantity']
            );

            $this->commandBus->handle($command);

            return $this->json(['message' => 'El articulo ha sido correctamente insertado en el carrito'], Response::HTTP_CREATED);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], $e->getCode());
        }
    }
}
