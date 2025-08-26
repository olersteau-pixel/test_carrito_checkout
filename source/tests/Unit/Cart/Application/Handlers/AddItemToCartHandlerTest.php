<?php


declare(strict_types=1);

namespace App\Tests\Unit\Cart\Application\Handlers;

use App\Cart\Application\Handlers\AddItemToCart\AddItemToCartCommand;
use App\Cart\Application\Handlers\AddItemToCart\AddItemToCartHandler;
use App\Cart\Domain\Entity\Cart;
use App\Cart\Domain\Entity\Product;
use App\Cart\Domain\Interfaces\CartRepositoryInterface;
use App\Cart\Domain\Interfaces\ProductRepositoryInterface;
use App\Cart\Domain\ValueObject\CartId;
use App\Cart\Domain\ValueObject\ProductId;
use App\Cart\Domain\Exception\ProductNotFoundException;
use App\Cart\Domain\Exception\CartNotFoundException;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

final class AddItemToCartHandlerTest extends TestCase
{
    private AddItemToCartHandler $handler;
    private CartRepositoryInterface|MockObject $cartRepository;
    private ProductRepositoryInterface|MockObject $productRepository;

    protected function setUp(): void
    {
        $this->cartRepository = $this->createMock(CartRepositoryInterface::class);
        $this->productRepository = $this->createMock(ProductRepositoryInterface::class);
        
        $this->handler = new AddItemToCartHandler(
            $this->cartRepository,
            $this->productRepository
        );
    }


    public function test_should_add_item_to_existing_cart(): void
    {
        $cartId = CartId::generate();
        $productId = ProductId::generate();
        $product = new Product($productId->value(), 'Test Product', 1000, 10);
        $cart = new Cart($cartId->value());
        
        $command = new AddItemToCartCommand($cartId->value(), $productId->value(), 3);
        
        $this->cartRepository
            ->expects($this->once())
            ->method('findById')
            ->with($cartId)
            ->willReturn($cart);
            
        $this->productRepository
            ->expects($this->once())
            ->method('findById')
            ->with($productId)
            ->willReturn($product);
            
        $this->cartRepository
            ->expects($this->once())
            ->method('save')
            ->with($cart);
        
        ($this->handler)($command);
        
        $this->assertTrue($cart->hasItem($productId->value()));
        $this->assertEquals(3, $cart->getItem($productId->value())->quantity());
    }

    public function test_should_throw_exception_when_product_not_found(): void
    {
        $cartId = CartId::generate();
        $productId = ProductId::generate();
        $cart = new Cart($cartId->value());

        $command = new AddItemToCartCommand($cartId->value(), $productId->value(), 2);
        
        $this->cartRepository
            ->expects($this->once())
            ->method('findById')
            ->willReturn($cart);
            
        $this->productRepository
            ->expects($this->once())
            ->method('findById')
            ->willReturn(null);
        
        $this->expectException(ProductNotFoundException::class);
        
        ($this->handler)($command);
    }

    public function test_should_throw_exception_when_add_item_to_not_found_cart(): void
    {
        $cartId = CartId::generate();
        $productId = ProductId::generate();
        $product = new Product($productId->value(), 'Test Product', 1000, 10);
        
        $command = new AddItemToCartCommand($cartId->value(), $productId->value(), 2);
        
        $this->cartRepository
            ->expects($this->once())
            ->method('findById')
            ->with($cartId)
            ->willReturn(null);
            
        
        $this->expectException(CartNotFoundException::class);

        ($this->handler)($command);
    }    
}