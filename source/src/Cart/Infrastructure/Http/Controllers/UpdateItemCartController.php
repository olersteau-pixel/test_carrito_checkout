<?php

declare(strict_types=1);

namespace App\Cart\Infrastructure\Http\Controllers;

use App\Cart\Application\Handlers\UpdateCartItem\UpdateCartItemCommand;
use App\Cart\Infrastructure\Http\Requests\UpdateItemRequest;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Shared\Application\Bus\CommandBusInterface;


final class UpdateItemCartController extends AbstractController
{
    public function __construct(
        private CommandBusInterface $commandBus,
    ) {
    }

    #[Route('/cart/{cartId}/items/{productId}', name: 'cart_update_item', methods: ['PUT'])]
    #[OA\Put(
        path: '/api/cart/{cartId}/items/{productId}',
        summary: 'Actualiza la cantidad de un producto',
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
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: 'object',
                properties: [
                    new OA\Property(property: 'quantity', type: 'integer', description: 'Cantidad nueva del producto'),
                ],
                required: ['quantity']
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Carrito actualizado'),
            new OA\Response(response: 400, description: 'Falta el campo de cantidad'),
            new OA\Response(response: 404, description: 'Producto no encontrado'),
        ],
        tags: ['Carrito']
    )]
    public function __invoke(Request $request, string $cartId, string $productId,
        DenormalizerInterface $serializer,
        ValidatorInterface $validator): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $addItemRequest = $serializer->denormalize(['cart_id' => $cartId, 'product_id' => $productId, ...$data], UpdateItemRequest::class);
            $errors = $validator->validate($addItemRequest);

            if (count($errors) > 0) {
                $errorMessages = [];
                foreach ($errors as $error) {
                    $errorMessages[] = $error->getMessage();
                }

                return $this->json(['error' => implode(',', $errorMessages)], Response::HTTP_BAD_REQUEST);
            }

            $command = new UpdateCartItemCommand(
                $cartId,
                $productId,
                (int) $data['quantity']
            );

            $this->commandBus->handle($command);

            return $this->json(['message' => 'Carrito actualizado']);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], $e->getCode());
        }
    }
}
