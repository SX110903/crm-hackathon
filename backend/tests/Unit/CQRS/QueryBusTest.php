<?php

namespace Tests\Unit\CQRS;

use App\Application\CQRS\Contracts\QueryHandlerInterface;
use App\Application\CQRS\Contracts\QueryInterface;
use App\Application\CQRS\QueryBus;
use RuntimeException;
use Tests\TestCase;

class QueryBusTest extends TestCase
{
    /** @test */
    public function it_dispatches_query_to_registered_handler(): void
    {
        $query      = new class implements QueryInterface {};
        $queryClass = get_class($query);

        $handler = new class implements QueryHandlerInterface {
            public bool $called = false;

            public function handle(QueryInterface $query): string
            {
                $this->called = true;

                return 'result';
            }
        };

        $bus = new QueryBus($this->app);
        $bus->register($queryClass, get_class($handler));
        $this->app->bind(get_class($handler), fn () => $handler);

        $result = $bus->dispatch($query);

        $this->assertTrue($handler->called);
        $this->assertSame('result', $result);
    }

    /** @test */
    public function it_throws_exception_when_no_handler_registered(): void
    {
        $this->expectException(RuntimeException::class);

        $query = new class implements QueryInterface {};
        $bus   = new QueryBus($this->app);
        $bus->dispatch($query);
    }
}
