<?php

declare(strict_types=1);

namespace App\Cart\Infrastructure\Http\Requests;

use Symfony\Component\Validator\Constraints as Assert;

final class RemoveItemRequest
{
    #[Assert\NotBlank(message: 'El ID del carrito es obligatorio.')]
    #[Assert\Uuid(
        message: 'El ID del carrito debe ser un UUID válido.'
    )]
    public mixed $cart_id;

    #[Assert\NotBlank(message: 'El ID del producto es obligatorio.')]
    #[Assert\Uuid(
        message: 'El ID del producto debe ser un UUID válido.'
    )]
    public mixed $product_id;
}
