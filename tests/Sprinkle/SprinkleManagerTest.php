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
use UserFrosting\Bakery\CommandReceipe;
use UserFrosting\Exceptions\SprinkleClassException;
use UserFrosting\Sprinkle\SprinkleManager;
use UserFrosting\Support\Exception\BadClassNameException;
use UserFrosting\Tests\TestSprinkle\TestSprinkle;

class SprinkleManagerTest extends TestCase
{
    public function testGetSprinklesWithNoDependent(): void
    {
        $manager = new SprinkleManager(CoreStub::class);
        $this->assertSame([CoreStub::class], $manager->getSprinkles());
    }

    /**
     * @depends testGetSprinklesWithNoDependent
     */
    public function testBadClassSprinkle(): void
    {
        $this->expectException(SprinkleClassException::class);
        $manager = new SprinkleManager(\stdClass::class);
    }

    public function testNonExistingClass(): void
    {
        $this->expectException(SprinkleClassException::class);
        $manager = new SprinkleManager(FooBar::class);
    }

    /**
     * @depends testGetSprinklesWithNoDependent
     */
    public function testBadClassSprinkleAsDependent(): void
    {
        $this->expectException(SprinkleClassException::class);
        $manager = new SprinkleManager(NotSprinkleStub::class);
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
     * @depends testGetSprinklesWithNoDependent
     */
    public function testGetBakeryCommandsWhenEmpty(): void
    {
        $manager = new SprinkleManager(CoreStub::class);
        $this->assertSame([], $manager->getBakeryCommands());
    }

    /**
     * @depends testGetBakeryCommandsWhenEmpty
     */
    public function testGetBakeryCommands(): void
    {
        $manager = new SprinkleManager(AdminStub::class);
        $this->assertSame([CommandStub::class], $manager->getBakeryCommands());
    }

    /**
     * N.B.: Command interface will be checked in Bakery, not SprinkleManager.
     * @depends testGetBakeryCommands
     */
    public function testGetBakeryCommandsWithBadCommand(): void
    {
        $manager = new SprinkleManager(AccountStub::class);
        $this->assertSame([
            \stdClass::class,
        ], $manager->getBakeryCommands());
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

class CommandStub extends CommandReceipe
{
    protected function configure()
    {
        $this->setName('stub');
    }
}
