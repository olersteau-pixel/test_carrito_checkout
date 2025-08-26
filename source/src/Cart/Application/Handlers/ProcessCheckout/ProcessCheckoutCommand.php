<?php

declare(strict_types=1);

namespace App\Cart\Application\Handlers\ProcessCheckout;

use App\Shared\Application\Bus\CommandInterface;

final class ProcessCheckoutCommand implements CommandInterface
{
    public function __construct(
        public readonly string $cartId,
        public readonly string $customerEmail,
    ) {
    }
}
