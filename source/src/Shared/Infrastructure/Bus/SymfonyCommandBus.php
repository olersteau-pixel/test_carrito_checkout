<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Bus;

use App\Shared\Application\Bus\CommandBusInterface;
use App\Shared\Application\Bus\CommandInterface;
use Symfony\Component\DependencyInjection\ServiceLocator;

class SymfonyCommandBus implements CommandBusInterface
{
    public function __construct(
        private ServiceLocator $commandHandlers
    ) {}

    public function handle(CommandInterface $command): void
    {
        $commandClass = get_class($command);
        
        $handlerName = $this->getHandlerName($commandClass);
        
        if (!$this->commandHandlers->has($handlerName)) {
            throw new \RuntimeException("Handler not found for command: {$commandClass}. Expected handler: {$handlerName}");
        }

        $handler = $this->commandHandlers->get($handlerName);
        $handler($command);
    }
    
    private function getHandlerName(string $commandClass): string
    {
        $handlerClass = str_replace('Command', 'Handler', $commandClass);
        
        return $handlerClass;
    }
}