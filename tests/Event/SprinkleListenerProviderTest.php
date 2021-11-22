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
use UserFrosting\Event\EventListenerRecipe;
use UserFrosting\Testing\TestCase;
use UserFrosting\Tests\TestSprinkle\TestSprinkle;

class SprinkleListenerProviderTest extends TestCase
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
            PizzaIsLate::class => [
                [LogPizza::class, 'onPizzaLate'],
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

        $event = $dispatcher->dispatch(new PizzaIsLate());
        $this->assertSame([
            'LogPizza::onPizzaLate',
        ], $event->passedThrough);
    }

    // TODO : Test Stopable events
}

/* Stub events */
class PizzaArrived
{
    public array $passedThrough = [];
}

class PizzaIsLate
{
    public array $passedThrough = [];
}

/* Stub Handler 1 */
class HandlePizza
{
    public function onPizzaArrived(PizzaArrived $event) : void
    {
        $event->passedThrough[] = 'HandlePizza::onPizzaArrived';
    }
    public function __invoke(PizzaArrived $event): void
    {
        $event->passedThrough[] = 'HandlePizza::__invoke';
    }
}

/* Stub Handler 2 */
class LogPizza
{
    public function onPizzaLate(PizzaIsLate $event) : void
    {
        $event->passedThrough[] = 'LogPizza::onPizzaLate';
    }
    public function onPizzaArrived(PizzaArrived $event) : void
    {
        $event->passedThrough[] = 'LogPizza::onPizzaArrived';
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
            ]
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
            PizzaIsLate::class => [
                [LogPizza::class, 'onPizzaLate'],
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
