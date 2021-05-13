<?php

/*
 * UserFrosting Framework (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/framework
 * @copyright Copyright (c) 2013-2021 Alexander Weissman, Louis Charette, Jordan Mele
 * @license   https://github.com/userfrosting/framework/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Tests\Bakery;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application;
use UserFrosting\Bakery\Bakery;
use UserFrosting\Bakery\CommandReceipe;
use UserFrosting\Exceptions\BakeryClassException;
use UserFrosting\Sprinkle\SprinkleReceipe;
use UserFrosting\Tests\TestSprinkle;

/**
 * Tests Bakery class.
 */
class BakeryTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testConstructor(): Bakery
    {
        $bakery = new Bakery(new TestSprinkle());
        $bakery->init();
        $this->assertInstanceOf(Bakery::class, $bakery);

        return $bakery;
    }

    /**
     * @depends testConstructor
     */
    public function testGetters(Bakery $bakery): void
    {
        $this->assertInstanceOf(Application::class, $bakery->getApp());
        $this->assertInstanceOf(ContainerInterface::class, $bakery->getContainer());
    }

    /**
     * @depends testConstructor
     */
    public function testCommandRegistration(): void
    {
        $mockCommand = new CommandStub();
        $mainSprinkle = m::mock(SprinkleReceipe::class);
        $mainSprinkle->shouldReceive('getSprinkles')->once()->andReturn([]);
        $mainSprinkle->shouldReceive('getBakeryCommands')->once()->andReturn([$mockCommand]);

        $bakery = new Bakery($mainSprinkle);
        $bakery->init();

        // TODO : Test commmand has been registered.
    }

    /**
     * @depends testConstructor
     */
    public function testBadCommandException(): void
    {
        $badCommand = new \stdClass();
        $mainSprinkle = m::mock(SprinkleReceipe::class);
        $mainSprinkle->shouldReceive('getSprinkles')->once()->andReturn([]);
        $mainSprinkle->shouldReceive('getBakeryCommands')->once()->andReturn([$badCommand]);

        $bakery = new Bakery($mainSprinkle);
        $this->expectException(BakeryClassException::class);
        $bakery->init();
    }
}

class CommandStub extends CommandReceipe
{
    protected function configure()
    {
        $this->setName('stub');
    }
}
