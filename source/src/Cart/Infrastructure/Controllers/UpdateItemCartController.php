<?php

declare(strict_types=1);

namespace App\Cart\Infrastructure\Controllers;

use App\Cart\Application\DTO\UpdateCartItemDTO;
use App\Cart\Application\Handlers\UpdateCartItem\UpdateCartItemHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UpdateItemCartController extends AbstractController
{
    public function __construct(
        private UpdateCartItemHandler $updateCartItemHandler,
    ) {
    }

    #[Route('/cart/{cartId}/items/{productId}', name: 'cart_update_item', methods: ['PUT'])]
    public function __invoke(Request $request, string $cartId, string $productId): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            if (!isset($data['quantity'])) {
                return $this->json(['error' => 'Falta el campo de cantidad'], Response::HTTP_BAD_REQUEST);
            }

            $dto = new UpdateCartItemDTO(
                $cartId,
                $productId,
                (int) $data['quantity']
            );

            ($this->updateCartItemHandler)($dto);

            return $this->json(['message' => 'Carrito actualizado']);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], $e->getCode());
        }
    }
}
