<?php

declare(strict_types=1);

namespace App\Command;

use App\Cart\Domain\Entity\Cart;
use App\Cart\Domain\Entity\Product;
use App\Cart\Domain\Interfaces\CartRepositoryInterface;
use App\Cart\Domain\Interfaces\ProductRepositoryInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:seed-products',
    description: 'Seed database with sample products',
)]
final class SeedProductsCommand extends Command
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private CartRepositoryInterface $cartRepository,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $carts = [
            '98e93f34-7cce-4fb5-bff8-1e3ac545dff1',
            '6222b469-a0e6-4b3d-9a30-5e2b809b9d42',
            'd2311b83-02ce-4ca5-9953-587616578aaf',
        ];

        $this->cartRepository->deleteAll();
        $io->progressStart(count($carts));
        foreach ($carts as $cartId) {
            $cart = new Cart(
                $cartId
            );

            $this->cartRepository->save($cart);
            $io->progressAdvance();
        }

        $io->progressFinish();
        foreach ($carts as $uuid) {
            $io->info(sprintf('Cart %s have been created', $uuid));
        }
        $io->success(sprintf('Successfully seeded %d carts!', count($carts)));

        $products = [
            ['41a562c5-f282-4c05-a01f-4602509f55c7', 'Portátil i7', 899],
            ['f9a29718-4a09-452c-a716-e963a11f8373', 'Ratón Wireless', 47.99],
            ['d9dc380c-6712-4f03-86e9-f43f58a57a9d', 'Teclado', 50],
            ['04d48c5a-00e5-4bb1-9997-df3062459b8f', 'Monitor 4K', 250],
            ['7c3d1c10-3d7f-42ee-a15b-30897cf640c2', 'Auriculares Gaming', 55],
            ['ddcd8834-de1e-401e-9829-8c0fe2e81616', 'Webcam HD', 110],
            ['ceae91d9-bf87-4d5f-b57e-eb4b77218546', 'Disco duro SSD 1TB', 90],
            ['9d8e35be-a21c-4a2a-9817-8811a09506ab', 'Memoria RAM 16GB', 80],
        ];

        $this->productRepository->deleteAll();
        $io->progressStart(count($products));
        foreach ($products as [$productId, $name, $price]) {
            $product = new Product(
                $productId,
                $name,
                $price
            );

            $this->productRepository->save($product);
            $io->progressAdvance();
        }

        $io->progressFinish();
        foreach ($products as [$uuid, $name, $price]) {
            $io->info(sprintf('Product %s (%s€) have id %s', $name, $price, $uuid));
        }
        $io->success(sprintf('Successfully seeded %d products!', count($products)));

        return Command::SUCCESS;
    }
}
