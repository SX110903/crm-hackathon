<?php

declare(strict_types=1);

namespace App\Application\CQRS;

use App\Application\CQRS\Contracts\CommandHandlerInterface;
use App\Application\CQRS\Contracts\CommandInterface;
use Illuminate\Contracts\Container\Container;
use RuntimeException;

/**
 * Singleton command bus: routes commands to their registered handlers.
 * Registered as a singleton in AppServiceProvider via the IoC container.
 */
final class CommandBus
{
    /** @var array<class-string, class-string<CommandHandlerInterface>> */
    private array $handlers = [];

    public function __construct(private readonly Container $container) {}

    /**
     * @param class-string                        $commandClass
     * @param class-string<CommandHandlerInterface> $handlerClass
     */
    public function register(string $commandClass, string $handlerClass): void
    {
        $this->handlers[$commandClass] = $handlerClass;
    }

    public function dispatch(CommandInterface $command): mixed
    {
        $commandClass = get_class($command);

        if (!isset($this->handlers[$commandClass])) {
            throw new RuntimeException("No handler registered for command [{$commandClass}].");
        }

        /** @var CommandHandlerInterface $handler */
        $handler = $this->container->make($this->handlers[$commandClass]);

        return $handler->handle($command);
    }
}
