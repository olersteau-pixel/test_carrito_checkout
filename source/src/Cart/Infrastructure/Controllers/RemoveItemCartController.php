<?php

declare(strict_types=1);

namespace App\Cart\Infrastructure\Controllers;

use App\Cart\Application\DTO\RemoveItemFromCartDTO;
use App\Cart\Application\Handlers\RemoveItemFromCart\RemoveItemFromCartHandler;
use App\Cart\Domain\Exception\ProductNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RemoveItemCartController extends AbstractController
{
    public function __construct(
        private RemoveItemFromCartHandler $removeItemFromCartHandler,
    ) {
    }

    #[Route('/cart/{cartId}/items/{productId}', name: 'cart_remove_item', methods: ['DELETE'])]
    public function __invoke(string $cartId, string $productId): JsonResponse
    {
        try {
            $dto = new RemoveItemFromCartDTO($cartId, $productId);
            ($this->removeItemFromCartHandler)($dto);

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
