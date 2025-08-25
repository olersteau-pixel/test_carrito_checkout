<?php

declare(strict_types=1);

namespace App\Cart\Infrastructure\Http\Requests;

use Symfony\Component\Validator\Constraints as Assert;

final class ProcessCheckoutRequest
{
    #[Assert\NotBlank]
    #[Assert\Email]
    public mixed $customer_email;

    #[Assert\NotBlank(message: 'El ID del carrito es obligatorio.')]
    #[Assert\Uuid(
        message: 'El ID del carrito debe ser un UUID válido.'
    )]
    public mixed $cart_id;
}
