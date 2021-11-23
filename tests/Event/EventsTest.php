<?php

declare(strict_types=1);

/*
 * UserFrosting Framework (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/framework
 * @copyright Copyright (c) 2013-2021 Alexander Weissman, Louis Charette, Jordan Mele
 * @license   https://github.com/userfrosting/framework/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Tests\Unit;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\EventDispatcher\StoppableEventInterface;
use UserFrosting\Event\EventListenerRecipe;
use UserFrosting\Testing\TestCase;
use UserFrosting\Tests\TestSprinkle\TestSprinkle;

class EventsTest extends TestCase
{
    protected string $mainSprinkle = SprinkleAStub::class;

    public function testGetRegisteredListeners(): void
    {
        /** @var ListenerProviderInterface */
        $provider = $this->ci->get(ListenerProviderInterface::class);

        $data = $provider->getRegisteredListeners();
        $this->assertSame([
            PizzaArrived::class => [
                HandlePizza::class,
                LogPizza::class,
                [LogPizza::class, 'onPizzaArrived'],
                HandlePizza::class,
                [HandlePizza::class, 'onPizzaArrived'],
            ],
            Pizza::class => [
                [LogPizza::class, 'onPizza'],
                [HandlePizza::class, 'isPizzaHot'],
            ],
            PizzaHasPineapple::class => [
                HandlePizza::class,
            ],
        ], $data);
    }

    /** @depends testGetRegisteredListeners */
    public function testIntegration(): void
    {
        /** @var EventDispatcherInterface */
        $dispatcher = $this->ci->get(EventDispatcherInterface::class);

        $event = $dispatcher->dispatch(new PizzaArrived());
        $this->assertSame([
            'HandlePizza::__invoke',
            'LogPizza::__invoke',
            'LogPizza::onPizzaArrived',
            'HandlePizza::__invoke',
            'HandlePizza::onPizzaArrived',
        ], $event->passedThrough);
    }

    /** @depends testIntegration */
    public function testStoppableEvent(): void
    {
        /** @var EventDispatcherInterface */
        $dispatcher = $this->ci->get(EventDispatcherInterface::class);

        // 'HandlePizza::isPizzaHot' is not here, as LogPizza stop execution
        $event = $dispatcher->dispatch(new Pizza());
        $this->assertSame([
            'LogPizza::onPizza',
        ], $event->passedThrough);

        // Even if it's registered, PizzaHasPineapple won't be passedThrough HandlePizza
        $event = $dispatcher->dispatch(new PizzaHasPineapple());
        $this->assertSame([], $event->passedThrough);
    }

    /** @depends testIntegration */
    public function testUnregisteredEvent(): void
    {
        /** @var EventDispatcherInterface */
        $dispatcher = $this->ci->get(EventDispatcherInterface::class);

        $pizzaIsCold = new PizzaIsCold();
        $event = $dispatcher->dispatch($pizzaIsCold);

        // Test dispatched event is returned untouched when no handler is present.
        $this->assertSame($pizzaIsCold, $event);
    }
}

/* Stub events */
class PizzaArrived
{
    public array $passedThrough = [];
}

class PizzaHasPineapple implements StoppableEventInterface
{
    public array $passedThrough = [];

    public function isPropagationStopped() : bool
    {
        return true;
    }
}

class Pizza implements StoppableEventInterface
{
    public array $passedThrough = [];

    public bool $stopped = false;

    public function isPropagationStopped() : bool
    {
        return $this->stopped;
    }
}

// Unregistered event
class PizzaIsCold
{
}

/* Stub Handler 1 */
class HandlePizza
{
    public function onPizzaArrived(PizzaArrived $event) : void
    {
        $event->passedThrough[] = 'HandlePizza::onPizzaArrived';
    }
    public function isPizzaHot(Pizza $event) : void
    {
        $event->passedThrough[] = 'HandlePizza::isPizzaHot';
    }
    public function __invoke(PizzaArrived $event): void
    {
        $event->passedThrough[] = 'HandlePizza::__invoke';
    }
}

/* Stub Handler 2 */
class LogPizza
{
    public function onPizzaArrived(PizzaArrived $event) : void
    {
        $event->passedThrough[] = 'LogPizza::onPizzaArrived';
    }
    public function onPizza(Pizza $event) : void
    {
        $event->passedThrough[] = 'LogPizza::onPizza';
        $event->stopped = true;
    }
    public function __invoke(PizzaArrived $event): void
    {
        $event->passedThrough[] = 'LogPizza::__invoke';
    }
}

/* Stub Sprinkle A */
class SprinkleAStub extends TestSprinkle implements EventListenerRecipe
{
    public static function getEventListeners(): array
    {
        return [
            PizzaArrived::class => [
                HandlePizza::class,
                [HandlePizza::class, 'onPizzaArrived'],
            ],
            PizzaHasPineapple::class => [
                HandlePizza::class,
            ],
            Pizza::class => [
                [HandlePizza::class, 'isPizzaHot'],
            ],
        ];
    }

    public static function getSprinkles(): array
    {
        return [
            SprinkleBStub::class,
            SprinkleCStub::class,
        ];
    }
}

/* Stub Sprinkle B */
class SprinkleBStub extends TestSprinkle implements EventListenerRecipe
{
    public static function getEventListeners(): array
    {
        return [
            PizzaArrived::class => [
                HandlePizza::class,
                LogPizza::class,
                [LogPizza::class, 'onPizzaArrived'],
            ],
            Pizza::class => [
                [LogPizza::class, 'onPizza'],
            ],
        ];
    }
}

/* Stub Sprinkle C => Not implementing EventListenerRecipe, won' be picked up */
class SprinkleCStub extends TestSprinkle
{
    public static function getEventListeners(): array
    {
        return [
            PizzaIsLate::class => [
                HandlePizza::class,
                [LogPizza::class, 'onPizzaLate'],
            ],
        ];
    }
}
