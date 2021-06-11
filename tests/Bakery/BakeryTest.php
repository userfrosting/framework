<?php

/*
 * UserFrosting Framework (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/framework
 * @copyright Copyright (c) 2013-2021 Alexander Weissman, Louis Charette, Jordan Mele
 * @license   https://github.com/userfrosting/framework/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Tests\Bakery;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use UserFrosting\Bakery\Bakery;
use UserFrosting\Exceptions\BakeryClassException;
use UserFrosting\Tests\TestSprinkle\TestSprinkle;

/**
 * Tests Bakery class.
 */
class BakeryTest extends TestCase
{
    public function testConstructor(): Bakery
    {
        $bakery = new Bakery(TestSprinkle::class);
        $this->assertInstanceOf(Bakery::class, $bakery);

        return $bakery;
    }

    /**
     * @depends testConstructor
     */
    public function testGetters(Bakery $bakery): void
    {
        $this->assertInstanceOf(Application::class, $bakery->getApp());
        $this->assertSame(TestSprinkle::class, $bakery->getMainSprinkle());
        $this->assertInstanceOf(ContainerInterface::class, $bakery->getContainer());
    }

    /**
     * @depends testConstructor
     */
    public function testCommandRegistration(): void
    {
        $bakery = new Bakery(SprinkleStub::class);
        $this->assertInstanceOf(Bakery::class, $bakery);

        $command = $bakery->getApp()->get('stub');
        $this->assertInstanceOf(CommandStub::class, $command);
    }

    /**
     * @depends testConstructor
     */
    public function testBadCommandException(): void
    {
        $this->expectException(BakeryClassException::class);
        $bakery = new Bakery(BadCommandSprinkleStub::class);
    }
}

class SprinkleStub extends TestSprinkle
{
    public static function getBakeryCommands(): array
    {
        return [
            CommandStub::class,
        ];
    }
}

class BadCommandSprinkleStub extends TestSprinkle
{
    public static function getBakeryCommands(): array
    {
        return [
            \stdClass::class,
        ];
    }
}

class CommandStub extends Command
{
    protected function configure()
    {
        $this->setName('stub');
    }
}
