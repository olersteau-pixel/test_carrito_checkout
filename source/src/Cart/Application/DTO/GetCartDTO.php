<?php

declare(strict_types=1);

namespace App\Cart\Application\DTO;

final class GetCartDTO
{
    public string $cartId;

    public function __construct(string $cartId)
    {
        $this->cartId = $cartId;
    }
}
