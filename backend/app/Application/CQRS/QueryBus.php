<?php

declare(strict_types=1);

namespace App\Application\CQRS;

use App\Application\CQRS\Contracts\QueryHandlerInterface;
use App\Application\CQRS\Contracts\QueryInterface;
use Illuminate\Contracts\Container\Container;
use RuntimeException;

/**
 * Singleton query bus: routes queries to their registered handlers.
 * Registered as a singleton in AppServiceProvider via the IoC container.
 */
final class QueryBus
{
    /** @var array<class-string, class-string<QueryHandlerInterface>> */
    private array $handlers = [];

    public function __construct(private readonly Container $container) {}

    /**
     * @param class-string                       $queryClass
     * @param class-string<QueryHandlerInterface> $handlerClass
     */
    public function register(string $queryClass, string $handlerClass): void
    {
        $this->handlers[$queryClass] = $handlerClass;
    }

    public function dispatch(QueryInterface $query): mixed
    {
        $queryClass = get_class($query);

        if (!isset($this->handlers[$queryClass])) {
            throw new RuntimeException("No handler registered for query [{$queryClass}].");
        }

        /** @var QueryHandlerInterface $handler */
        $handler = $this->container->make($this->handlers[$queryClass]);

        return $handler->handle($query);
    }
}
