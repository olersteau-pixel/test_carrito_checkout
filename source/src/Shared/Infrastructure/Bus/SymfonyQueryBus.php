<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Bus;

use App\Shared\Application\Bus\QueryBusInterface;
use App\Shared\Application\Bus\QueryInterface;
use Symfony\Component\DependencyInjection\ServiceLocator;

class SymfonyQueryBus implements QueryBusInterface
{
    public function __construct(
        private ServiceLocator $queryHandlers
    ) {}

    public function handle(QueryInterface $query): mixed
    {
        $queryClass = get_class($query);
        $handlerName = str_replace('Query', 'Handler', $queryClass);
        
        if (!$this->queryHandlers->has($handlerName)) {
            throw new \RuntimeException("Handler not found for query: {$queryClass}");
        }

        $handler = $this->queryHandlers->get($handlerName);
        return $handler($query);
    }
}