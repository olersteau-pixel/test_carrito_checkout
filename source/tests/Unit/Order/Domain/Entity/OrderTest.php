<?php


declare(strict_types=1);

namespace App\Tests\Unit\Order\Domain\Entity;

use App\Cart\Domain\Entity\Cart;
use App\Cart\Domain\Entity\Product;
use App\Cart\Domain\ValueObject\CartId;
use App\Cart\Domain\ValueObject\ProductId;
use App\Order\Domain\Entity\Order;
use App\Order\Domain\ValueObject\OrderId;
use App\Order\Domain\Enum\OrderStatus;
use PHPUnit\Framework\TestCase;

final class OrderTest extends TestCase
{
    public function test_should_create_order_from_cart(): void
    {
        $cartId = CartId::generate();
        $cart = new Cart($cartId->value());
        
        $product1 = new Product(ProductId::generate()->value(), 'Product 1',1000, 10);
        $product2 = new Product(ProductId::generate()->value(), 'Product 2', 2000, 5);
        
        $cart->addItem($cart, $product1, 2);
        $cart->addItem($cart, $product2, 1);
        
        $orderId = OrderId::generate();
        $customerEmail = 'test@example.com';
        
        $order = Order::fromCart($orderId, $customerEmail, $cart);

        $this->assertEquals($orderId->value(), $order->id());
        $this->assertEquals($customerEmail, $order->customerEmail());
        $this->assertEquals(self::getStatus(OrderStatus::PENDING), $order->status());
        $this->assertEquals(4000, $order->totalAmount()); 
        $this->assertCount(2, $order->items());
    }

    public function test_should_confirm_order(): void
    {
        $cart = new Cart(CartId::generate()->value());
        $product = new Product(ProductId::generate()->value(), 'Product',1000, 10);
        $cart->addItem($cart, $product, 1);
        
        $order = Order::fromCart(OrderId::generate(), 'test@example.com', $cart);
        
        $this->assertEquals(self::getStatus(OrderStatus::PENDING), $order->status());
        
        $order->confirm();
        
        $this->assertEquals(self::getStatus(OrderStatus::CONFIRMED), $order->status());
    }

    public function test_should_cancel_order(): void
    {
        $cart = new Cart(CartId::generate()->value());
        $product = new Product(ProductId::generate()->value(), 'Product', 1000, 10);
        $cart->addItem($cart, $product, 1);
        
        $order = Order::fromCart(OrderId::generate(), 'test@example.com', $cart);
        
        $order->cancel();
        
        $this->assertEquals(self::getStatus(OrderStatus::CANCELLED), $order->status());
    }

    private static function getStatus(OrderStatus $status) {
        return $status->value;
    }
}