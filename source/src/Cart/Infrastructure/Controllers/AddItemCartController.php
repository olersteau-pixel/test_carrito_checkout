<?php

declare(strict_types=1);

namespace App\Cart\Infrastructure\Controllers;

use App\Cart\Application\DTO\AddItemToCartDTO;
use App\Cart\Application\Handlers\AddItemToCart\AddItemToCartHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AddItemCartController extends AbstractController
{
    public function __construct(
        private AddItemToCartHandler $addItemHandler,
    ) {
    }

    #[Route('/cart/{cartId}/items', name: 'cart_add_item', methods: ['POST'])]
    public function __invoke(Request $request, string $cartId): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            if (!isset($data['product_id']) || !isset($data['quantity'])) {
                return $this->json(['error' => 'Faltan campos'], Response::HTTP_BAD_REQUEST);
            }

            $dto = new AddItemToCartDTO(
                $cartId,
                $data['product_id'],
                (int) $data['quantity']
            );

            ($this->addItemHandler)($dto);

            return $this->json(['message' => 'El articulo ha sido correctamente insertado en el carrito'], Response::HTTP_CREATED);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], $e->getCode());
        }
    }
}
