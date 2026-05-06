<?php

namespace Tests\Unit\CQRS;

use App\Application\CQRS\CommandBus;
use App\Application\CQRS\Contracts\CommandHandlerInterface;
use App\Application\CQRS\Contracts\CommandInterface;
use RuntimeException;
use Tests\TestCase;

class CommandBusTest extends TestCase
{
    /** @test */
    public function it_dispatches_command_to_registered_handler(): void
    {
        $command      = new class implements CommandInterface {};
        $commandClass = get_class($command);

        $handler = new class implements CommandHandlerInterface {
            public bool $called = false;

            public function handle(CommandInterface $command): string
            {
                $this->called = true;

                return 'ok';
            }
        };

        $bus = new CommandBus($this->app);
        $bus->register($commandClass, get_class($handler));
        $this->app->bind(get_class($handler), fn () => $handler);

        $result = $bus->dispatch($command);

        $this->assertTrue($handler->called);
        $this->assertSame('ok', $result);
    }

    /** @test */
    public function it_throws_exception_when_no_handler_registered(): void
    {
        $this->expectException(RuntimeException::class);

        $command = new class implements CommandInterface {};
        $bus     = new CommandBus($this->app);
        $bus->dispatch($command);
    }
}
