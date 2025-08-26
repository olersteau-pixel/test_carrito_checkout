<?php

declare(strict_types=1);

namespace App\Cart\Infrastructure\Http\Controllers;

use App\Cart\Application\Handlers\RemoveItemFromCart\RemoveItemFromCartCommand;
use App\Cart\Application\Handlers\RemoveItemFromCart\RemoveItemFromCartHandler;
use App\Cart\Domain\Exception\ProductNotFoundException;
use App\Cart\Infrastructure\Http\Requests\RemoveItemRequest;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Shared\Application\Bus\CommandBusInterface;

final class RemoveItemCartController extends AbstractController
{
    public function __construct(
        private CommandBusInterface $commandBus,
    ) {
    }

    #[Route('/cart/{cartId}/items/{productId}', name: 'cart_remove_item', methods: ['DELETE'])]
    #[OA\Delete(
        path: '/api/cart/{cartId}/items/{productId}',
        summary: 'Elimina un producto del carrito',
        parameters: [
            new OA\Parameter(
                name: 'cartId',
                in: 'path',
                description: 'ID del carrito',
                required: true,
                schema: new OA\Schema(type: 'string', format: 'uuid')
            ),
            new OA\Parameter(
                name: 'productId',
                in: 'path',
                description: 'ID del producto',
                required: true,
                schema: new OA\Schema(type: 'string', format: 'uuid')
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'El producto ha sido eliminado del carrito'),
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
    public function __invoke(string $cartId, string $productId,
        SerializerInterface $serializer,
        ValidatorInterface $validator): JsonResponse
    {
        try {
            $addItemRequest = $serializer->denormalize(['cart_id' => $cartId, 'product_id' => $productId], RemoveItemRequest::class);
            $errors = $validator->validate($addItemRequest);

            if (count($errors) > 0) {
                $errorMessages = [];
                foreach ($errors as $error) {
                    $errorMessages[] = $error->getMessage();
                }

                return $this->json(['error' => implode(',', $errorMessages)], Response::HTTP_BAD_REQUEST);
            }

            $command = new RemoveItemFromCartCommand($cartId, $productId);
            $this->commandBus->handle($command);

            return $this->json(['message' => 'El producto ha sido eliminado del carrito']);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (ProductNotFoundException $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], $e->getCode());
        }
    }
}
