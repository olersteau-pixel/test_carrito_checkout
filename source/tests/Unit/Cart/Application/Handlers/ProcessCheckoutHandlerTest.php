<?php


declare(strict_types=1);

namespace App\Tests\Unit\Cart\Application\Handlers;

use App\Cart\Application\DTO\ProcessCheckoutDTO;
use App\Cart\Application\Handlers\ProcessCheckout\ProcessCheckoutHandler;
use App\Cart\Domain\Entity\Cart;
use App\Cart\Domain\Entity\Product;
use App\Cart\Domain\Interfaces\CartRepositoryInterface;
use App\Cart\Domain\ValueObject\CartId;
use App\Cart\Domain\ValueObject\ProductId;
use App\Order\Domain\Interfaces\OrderRepositoryInterface;
use App\Cart\Domain\Exception\CartNotFoundException;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

final class ProcessCheckoutHandlerTest extends TestCase
{
    private ProcessCheckoutHandler $handler;
    private CartRepositoryInterface|MockObject $cartRepository;
    private OrderRepositoryInterface|MockObject $orderRepository;

    protected function setUp(): void
    {
        $this->cartRepository = $this->createMock(CartRepositoryInterface::class);
        $this->orderRepository = $this->createMock(OrderRepositoryInterface::class);
        
        $this->handler = new ProcessCheckoutHandler(
            $this->cartRepository,
            $this->orderRepository
        );
    }

    public function test_should_process_successful_checkout(): void
    {
        $cartId = CartId::generate();
        $productId = ProductId::generate();
        $product = new Product($productId->value(), 'Test Product', 1000, 10);
        $cart = new Cart($cartId->value());
        $cart->addItem($cart, $product, 2);
        
        $dto = new ProcessCheckoutDTO($cartId->value(), 'test@example.com');
        $this->cartRepository
            ->expects($this->once())
            ->method('findById')
            ->willReturn($cart);
            
        $this->orderRepository
            ->expects($this->once())
            ->method('save');
            
        $this->cartRepository
            ->expects($this->once())
            ->method('save')
            ->with($cart);
        
        $orderId = ($this->handler)($dto);
        
        $this->assertNotEmpty($orderId);
        $this->assertTrue($cart->isEmpty());
    }

    public function test_should_throw_exception_when_cart_not_found(): void
    {
        $cartId = CartId::generate();
        $dto = new ProcessCheckoutDTO($cartId->value(), 'test@example.com');
        
        $this->cartRepository
            ->expects($this->once())
            ->method('findById')
            ->willReturn(null);
        
        $this->expectException(CartNotFoundException::class);
        
        ($this->handler)($dto);
    }

    public function test_should_throw_exception_when_cart_is_empty(): void
    {
        $cartId = CartId::generate();
        $cart = new Cart($cartId->value());
        $dto = new ProcessCheckoutDTO($cartId->value(), 'test@example.com');
        
        $this->cartRepository
            ->expects($this->once())
            ->method('findById')
            ->willReturn($cart);
        
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('El carrito esta vacio, no se puede procesar');
        
        ($this->handler)($dto);
    }
}