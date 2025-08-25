<?php

declare(strict_types=1);

namespace App\Cart\Infrastructure\Controllers;

use App\Cart\Application\DTO\ProcessCheckoutDTO;
use App\Cart\Application\Handlers\ProcessCheckout\ProcessCheckoutHandler;
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
