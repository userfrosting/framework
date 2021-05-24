<?php

/*
 * UserFrosting Framework (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/framework
 * @copyright Copyright (c) 2013-2021 Alexander Weissman, Louis Charette, Jordan Mele
 * @license   https://github.com/userfrosting/framework/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Tests\Unit;

use Closure;
use PHPUnit\Framework\TestCase;
use UserFrosting\Bakery\CommandReceipe;
use UserFrosting\Exceptions\BakeryClassException;
use UserFrosting\Exceptions\SprinkleClassException;
use UserFrosting\Sprinkle\SprinkleManager;
use UserFrosting\Exceptions\BadInstanceOfException;
use UserFrosting\Support\Exception\FileNotFoundException;
use UserFrosting\Tests\TestSprinkle\TestSprinkle;

class SprinkleManagerTest extends TestCase
{
    /**
     * getSprinkles
     */
    public function testGetSprinklesWithNoDependent(): void
    {
        $manager = new SprinkleManager(CoreStub::class);
        $this->assertSame([CoreStub::class], $manager->getSprinkles());
    }

    /**
     * @depends testGetSprinklesWithNoDependent
     */
    public function testGetSprinklesWithManyDependent(): void
    {
        $manager = new SprinkleManager(MainStub::class);
        $this->assertCount(4, $manager->getSprinkles());
        $this->assertSame([
            MainStub::class,
            CoreStub::class,
            AdminStub::class,
            AccountStub::class,
        ], $manager->getSprinkles());
    }

    /**
     * @depends testGetSprinklesWithManyDependent
     */
    public function testGetSprinklesWithNestedDependent(): void
    {
        $manager = new SprinkleManager(MainNestedStub::class);
        $this->assertCount(4, $manager->getSprinkles());
        $this->assertSame([
            MainNestedStub::class,
            CoreStub::class,
            AdminNestedStub::class,
            AccountStub::class,
        ], $manager->getSprinkles());
    }

    /**
     * @depends testGetSprinklesWithNestedDependent
     */
    public function testGetSprinklesWithDuplicateSprinkles(): void
    {
        $manager = new SprinkleManager(MainDuplicateStub::class);
        $this->assertCount(4, $manager->getSprinkles());
        $this->assertSame([
            MainDuplicateStub::class,
            CoreStub::class,
            AdminNestedStub::class,
            AccountStub::class,
        ], $manager->getSprinkles());
    }

    /**
     * __construct & Exceptions
     */
    public function testConstructorWithBadSprinkleClass(): void
    {
        $this->expectException(SprinkleClassException::class);
        $manager = new SprinkleManager(\stdClass::class);
    }

    public function testConstructorWithNonExistingClass(): void
    {
        $this->expectException(SprinkleClassException::class);
        $manager = new SprinkleManager(FooBar::class);
    }

    /**
     * @depends testConstructorWithBadSprinkleClass
     * @depends testGetSprinklesWithManyDependent
     */
    public function testConstructorWithBadSprinkleClassInDependent(): void
    {
        $this->expectException(SprinkleClassException::class);
        $manager = new SprinkleManager(NotSprinkleStub::class);
    }

    /**
     * getBakeryCommands
     *
     * @depends testGetSprinklesWithNoDependent
     */
    public function testGetBakeryCommandsWithNoEmpty(): void
    {
        $manager = new SprinkleManager(CoreStub::class);
        $this->assertSame([], $manager->getBakeryCommands());
    }

    /**
     * @depends testGetBakeryCommandsWithNoEmpty
     */
    public function testGetBakeryCommands(): void
    {
        $manager = new SprinkleManager(AdminStub::class);
        $commands = $manager->getBakeryCommands();

        $this->assertIsArray($commands);
        $this->assertCount(1, $commands);
        $this->assertInstanceOf(CommandReceipe::class, $commands[0]);
    }

    /**
     * @depends testGetBakeryCommands
     */
    public function testGetBakeryCommandsWithBadCommand(): void
    {
        $manager = new SprinkleManager(AccountStub::class);
        $this->expectException(BakeryClassException::class);
        $manager->getBakeryCommands();
    }

    /**
     * Test routes
     */
    public function testGetRoutesDefinitions(): void
    {
        $manager = new SprinkleManager(TestSprinkle::class);
        $routes = $manager->getRoutesDefinitions();

        $this->assertIsArray($routes);
        $this->assertCount(1, $routes);
        $this->assertInstanceOf(Closure::class, $routes[0]);
    }

    /**
     * @depends testGetRoutesDefinitions
     */
    public function testGetRoutesDefinitionsWithNotFoundFile(): void
    {
        $manager = new SprinkleManager(FileNotFoundSprinkleStub::class);
        $this->expectException(FileNotFoundException::class);
        $manager->getRoutesDefinitions();
    }

    /**
     * @depends testGetRoutesDefinitions
     */
    public function testGetRoutesDefinitionsWithBadInstance(): void
    {
        $manager = new SprinkleManager(BadInstanceOfSprinkleStub::class);
        $this->expectException(BadInstanceOfException::class);
        $manager->getRoutesDefinitions();
    }

    /**
     * Test Services
     */
    public function testGetServicesDefinitions(): void
    {
        $manager = new SprinkleManager(TestSprinkle::class);
        $services = $manager->getServicesDefinitions();

        $this->assertIsArray($services);
        $this->assertCount(1, $services);
        $this->assertArrayHasKey('testMessageGenerator', $services);
    }

    /**
     * @depends testGetServicesDefinitions
     */
    public function testGetServicesDefinitionsWithNotFoundFile(): void
    {
        $manager = new SprinkleManager(FileNotFoundSprinkleStub::class);
        $this->expectException(FileNotFoundException::class);
        $manager->getServicesDefinitions();
    }

    /**
     * @depends testGetServicesDefinitions
     */
    public function testGetServicesDefinitionsWithBadInstance(): void
    {
        $manager = new SprinkleManager(BadInstanceOfSprinkleStub::class);
        $this->expectException(BadInstanceOfException::class);
        $manager->getServicesDefinitions();
    }
}

class CoreStub extends TestSprinkle
{
}

class AdminStub extends TestSprinkle
{
    public static function getBakeryCommands(): array
    {
        return [
            CommandStub::class,
        ];
    }
}

class AdminNestedStub extends TestSprinkle
{
    public static function getSprinkles(): array
    {
        return [
            AccountStub::class,
        ];
    }
}

class AccountStub extends TestSprinkle
{
    public static function getBakeryCommands(): array
    {
        return [
            \stdClass::class,
        ];
    }
}

class MainStub extends TestSprinkle
{
    public static function getSprinkles(): array
    {
        return [
            CoreStub::class,
            AdminStub::class,
            AccountStub::class,
        ];
    }
}

class MainNestedStub extends TestSprinkle
{
    public static function getSprinkles(): array
    {
        return [
            CoreStub::class,
            AdminNestedStub::class,
        ];
    }
}

class MainDuplicateStub extends TestSprinkle
{
    public static function getSprinkles(): array
    {
        return [
            CoreStub::class,
            AdminNestedStub::class,
            AccountStub::class,
        ];
    }
}

class NotSprinkleStub extends TestSprinkle
{
    public static function getSprinkles(): array
    {
        return [
            \stdClass::class,
        ];
    }
}

class FileNotFoundSprinkleStub extends TestSprinkle
{
    public static function getRoutes(): array
    {
        return ['foo/'];
    }

    public static function getServices(): array
    {
        return ['foo/'];
    }
}

class BadInstanceOfSprinkleStub extends TestSprinkle
{
    public static function getRoutes(): array
    {
        return [self::getPath() . '/routes/badRoutes.php'];
    }

    public static function getServices(): array
    {
        return [self::getPath() . '/container/badServices.php'];
    }
}

class CommandStub extends CommandReceipe
{
    protected function configure()
    {
        $this->setName('stub');
    }
}
