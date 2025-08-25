<?php


declare(strict_types=1);

namespace App\Tests\Unit\Cart\Application\Handlers;

use App\Cart\Application\DTO\GetCartDTO;
use App\Cart\Application\Handlers\GetCart\GetCartHandler;
use App\Cart\Domain\Entity\Cart;
use App\Cart\Domain\Entity\CartItem;
use App\Cart\Domain\Entity\Product;
use App\Cart\Domain\Interfaces\CartRepositoryInterface;
use App\Cart\Domain\Interfaces\ProductRepositoryInterface;
use App\Cart\Domain\ValueObject\CartId;
use App\Cart\Domain\ValueObject\ProductId;
use App\Cart\Domain\Exception\ProductNotFoundException;
use App\Cart\Domain\Exception\CartNotFoundException;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

final class GetCartHandlerTest extends TestCase
{
    private GetCartHandler $handler;
    private CartRepositoryInterface|MockObject $cartRepository;

    protected function setUp(): void
    {
        $this->cartRepository = $this->createMock(CartRepositoryInterface::class);
        
        $this->handler = new GetCartHandler(
            $this->cartRepository
        );
    }


    public function test_should_get_existing_cart(): void
    {
        $cartId = CartId::generate();
        $cart = new Cart($cartId->value());    
        $dto = new GetCartDTO($cartId->value());

        $this->cartRepository
            ->expects($this->once())
            ->method('findById')
            ->with($cartId)
            ->willReturn($cart);
        
        ($this->handler)($dto);
    }

    public function test_should_throw_exception_when_add_item_to_not_found_cart(): void
    {
        $cartId = CartId::generate();
        
        $dto = new GetCartDTO($cartId->value());
        
        $this->cartRepository
            ->expects($this->once())
            ->method('findById')
            ->with($cartId)
            ->willReturn(null);
            
        
        $this->expectException(CartNotFoundException::class);

        ($this->handler)($dto);
    }    
}