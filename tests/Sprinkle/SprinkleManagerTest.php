<?php

/*
 * UserFrosting Framework (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/framework
 * @copyright Copyright (c) 2013-2021 Alexander Weissman, Louis Charette, Jordan Mele
 * @license   https://github.com/userfrosting/framework/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use UserFrosting\Exceptions\BadInstanceOfException;
use UserFrosting\Exceptions\SprinkleClassException;
use UserFrosting\Sprinkle\SprinkleManager;
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
        $this->assertSame('Test Sprinkle', $manager->getSprinkles()[0]::getName());

        // Test getMainSprinkle while at it
        $this->assertSame(CoreStub::class, $manager->getMainSprinkle());
        $this->assertSame('Test Sprinkle', $manager->getMainSprinkle()::getName());

        // Test isAvaialable while at it
        $this->assertTrue($manager->isAvailable(CoreStub::class));
        $this->assertFalse($manager->isAvailable(AdminStub::class));
    }

    /**
     * @depends testGetSprinklesWithNoDependent
     */
    public function testGetSprinklesWithManyDependent(): void
    {
        $manager = new SprinkleManager(MainStub::class);
        $this->assertCount(4, $manager->getSprinkles());
        $this->assertSame([
            CoreStub::class,
            AdminStub::class,
            AccountStub::class,
            MainStub::class,
        ], $manager->getSprinkles());
        $this->assertSame(MainStub::class, $manager->getMainSprinkle());

        // Test getSprinkleNames while at it
        $this->assertSame([
            'Test Sprinkle',
            'Test Sprinkle',
            'Test Sprinkle',
            'Main Sprinkle',
        ], $manager->getSprinklesNames());
    }

    /**
     * @depends testGetSprinklesWithManyDependent
     */
    public function testGetSprinklesWithNestedDependent(): void
    {
        $manager = new SprinkleManager(MainNestedStub::class);
        $this->assertCount(4, $manager->getSprinkles());
        $this->assertSame([
            CoreStub::class,
            AccountStub::class,
            AdminNestedStub::class,
            MainNestedStub::class,
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
            CoreStub::class,
            AccountStub::class,
            AdminNestedStub::class,
            MainDuplicateStub::class,
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
}

class MainStub extends TestSprinkle
{
    public static function getName(): string
    {
        return 'Main Sprinkle';
    }

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

class BadInstanceOfSprinkleStub extends TestSprinkle
{
    public static function getRoutes(): array
    {
        return [\stdClass::class];
    }

    public static function getServices(): array
    {
        return [\stdClass::class];
    }

    public static function getBakeryCommands(): array
    {
        return [\stdClass::class];
    }

    public static function getMiddlewares(): array
    {
        return [\stdClass::class];
    }
}

class NotFoundStub extends TestSprinkle
{
    public static function getBakeryCommands(): array
    {
        return [NotFound::class];
    }

    public static function getMiddlewares(): array
    {
        return [NotFound::class];
    }
}

class CommandStub extends Command
{
    protected function configure()
    {
        $this->setName('stub');
    }
}
