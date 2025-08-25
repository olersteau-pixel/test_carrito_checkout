<?php

declare(strict_types=1);

namespace App\Cart\Application\DTO;

final class ProcessCheckoutDTO
{
    public string $cartId;
    public string $customerEmail;

    public function __construct(string $cartId, string $customerEmail)
    {
        $this->cartId = $cartId;
        $this->customerEmail = $customerEmail;
    }
}
