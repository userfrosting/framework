<?php

/*
 * UserFrosting Framework (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/framework
 * @copyright Copyright (c) 2013-2021 Alexander Weissman, Louis Charette, Jordan Mele
 * @license   https://github.com/userfrosting/framework/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Tests\Unit;

use DI\Container;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use UserFrosting\Exceptions\BadInstanceOfException;
use UserFrosting\Sprinkle\RecipeExtensionLoader;
use UserFrosting\Sprinkle\SprinkleManager;
use UserFrosting\Sprinkle\SprinkleRecipe;
use UserFrosting\Support\Exception\ClassNotFoundException;
use UserFrosting\Tests\TestSprinkle\TestSprinkle;

class RecipeExtensionLoaderTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * Purpose of this test is to create a reusable $loader instance so the
     * same code is not reused too much.
     */
    public function testConstructor(): RecipeExtensionLoader
    {
        $ci = Mockery::mock(Container::class);
        $manager = Mockery::mock(SprinkleManager::class);

        $loader = new RecipeExtensionLoader($manager, $ci);

        $this->assertInstanceOf(RecipeExtensionLoader::class, $loader);

        return $loader;
    }

    /**
     * @depends testConstructor
     *
     * @param RecipeExtensionLoader $loader
     */
    public function testValidate(RecipeExtensionLoader $loader): void
    {
        $this->assertTrue($loader->validateClass(RecipeExtensionLoaderStub::class));
    }

    /**
     * @depends testConstructor
     *
     * @param RecipeExtensionLoader $loader
     */
    public function testValidateWithInterface(RecipeExtensionLoader $loader): void
    {
        $isValid = $loader->validateClass(SprinkleStub::class, SprinkleRecipe::class);
        $this->assertTrue($isValid);
    }

    /**
     * @depends testConstructor
     *
     * @param RecipeExtensionLoader $loader
     */
    public function testValidateWithSubclass(RecipeExtensionLoader $loader): void
    {
        $isValid = $loader->validateClass(RecipeExtensionLoaderStubExtended::class, RecipeExtensionLoaderStub::class);
        $this->assertTrue($isValid);
    }

    /**
     * @depends testConstructor
     *
     * @param RecipeExtensionLoader $loader
     */
    public function testValidateWithBadSubclass(RecipeExtensionLoader $loader): void
    {
        $result = $loader->validateClass(\stdClass::class, RecipeExtensionLoaderStub::class);
        $this->assertFalse($result);
    }

    /**
     * @depends testConstructor
     *
     * @param RecipeExtensionLoader $loader
     */
    public function testValidateWithBadSubclassWithThrowInterface(RecipeExtensionLoader $loader): void
    {
        $this->expectException(BadInstanceOfException::class);
        $loader->validateClass(\stdClass::class, RecipeExtensionLoaderStub::class, true);
    }

    /**
     * @depends testConstructor
     *
     * @param RecipeExtensionLoader $loader
     */
    public function testValidateClassNotFound(RecipeExtensionLoader $loader): void
    {
        $this->expectException(ClassNotFoundException::class);
        $loader->validateClass(Bar::class);
    }

    /**
     * @depends testConstructor
     *
     * @param RecipeExtensionLoader $loader
     */
    public function testValidateWithBadInterface(RecipeExtensionLoader $loader): void
    {
        $result = $loader->validateClass(SprinkleStub::class, ContainerInterface::class);
        $this->assertFalse($result);
    }

    /**
     * @depends testConstructor
     *
     * @param RecipeExtensionLoader $loader
     */
    public function testValidateWithBadInterfaceWithThrowInterface(RecipeExtensionLoader $loader): void
    {
        $this->expectException(BadInstanceOfException::class);
        $loader->validateClass(SprinkleStub::class, ContainerInterface::class, true);
    }

    /**
     * We can now test getInstances.
     *
     * @depends testValidate
     */
    public function testGetInstances(): void
    {
        $ci = Mockery::mock(Container::class)
            ->shouldReceive('get')
            ->with(RecipeExtensionLoaderStub::class)
            ->once()
            ->andReturn(new RecipeExtensionLoaderStub())
            ->getMock();

        $manager = Mockery::mock(SprinkleManager::class)
            ->shouldReceive('getSprinkles')->once()->andReturn([SprinkleStub::class])
            ->getMock();

        $loader = new RecipeExtensionLoader($manager, $ci);

        $instances = $loader->getInstances('getFoo');

        $this->assertIsArray($instances);
        $this->assertCount(1, $instances);
        $this->assertInstanceOf(RecipeExtensionLoaderStub::class, $instances[0]);
    }

    /**
     * We can now test getInstances.
     *
     * @depends testGetInstances
     */
    public function testGetInstancesWithNoRecipe(): void
    {
        $ci = Mockery::mock(Container::class)
            ->shouldNotReceive('get')->with(RecipeExtensionLoaderStub::class)
            ->getMock();

        $manager = Mockery::mock(SprinkleManager::class)
            ->shouldReceive('getSprinkles')->once()->andReturn([SprinkleStubB::class])
            ->getMock();

        $loader = new RecipeExtensionLoader($manager, $ci);

        $instances = $loader->getInstances('getFoo', CustomRecipeInterface::class);

        $this->assertIsArray($instances);
        $this->assertCount(0, $instances);
    }

    /**
     * We can now test getInstances.
     *
     * @depends testGetInstances
     */
    public function testGetInstancesWithTwoSprinkles(): void
    {
        $ci = Mockery::mock(Container::class)
            ->shouldReceive('get')
            ->with(RecipeExtensionLoaderStub::class)
            ->once()
            ->andReturn(new RecipeExtensionLoaderStub())
            ->getMock();

        $manager = Mockery::mock(SprinkleManager::class)
            ->shouldReceive('getSprinkles')->once()->andReturn([SprinkleStub::class, SprinkleStubB::class])
            ->getMock();

        $loader = new RecipeExtensionLoader($manager, $ci);

        $instances = $loader->getInstances('getFoo', CustomRecipeInterface::class);

        $this->assertIsArray($instances);
        $this->assertCount(1, $instances);
        $this->assertInstanceOf(RecipeExtensionLoaderStub::class, $instances[0]);
    }

    /**
     * Make sure recipeInterface is correctly passed.
     *
     * @depends testValidateWithBadInterface
     */
    public function testGetInstancesWithBadRecipeInterface(): void
    {
        $ci = Mockery::mock(Container::class);

        $manager = Mockery::mock(SprinkleManager::class)
            ->shouldReceive('getSprinkles')->once()->andReturn([SprinkleStub::class])
            ->getMock();

        $loader = new RecipeExtensionLoader($manager, $ci);

        $result = $loader->getInstances('getFoo', recipeInterface: ContainerInterface::class);
        $this->assertSame([], $result);
    }

    /**
     * Make sure recipeInterface is correctly passed.
     *
     * @depends testValidateWithBadInterface
     */
    public function testGetInstancesWithBadRecipeInterfaceWithThrowInterface(): void
    {
        $ci = Mockery::mock(Container::class);

        $manager = Mockery::mock(SprinkleManager::class)
            ->shouldReceive('getSprinkles')->once()->andReturn([SprinkleStub::class])
            ->getMock();

        $loader = new RecipeExtensionLoader($manager, $ci);

        $this->expectException(BadInstanceOfException::class);
        $loader->getInstances('getFoo', recipeInterface: ContainerInterface::class, throwBadInterface: true);
    }

    /**
     * Make sure extensionInterface is correctly passed.
     *
     * @depends testValidateWithBadInterface
     */
    public function testGetInstancesWithBadExtensionInterface(): void
    {
        $ci = Mockery::mock(Container::class);

        $manager = Mockery::mock(SprinkleManager::class)
            ->shouldReceive('getSprinkles')->once()->andReturn([SprinkleStub::class])
            ->getMock();

        $loader = new RecipeExtensionLoader($manager, $ci);

        $this->expectException(BadInstanceOfException::class);
        $loader->getInstances('getFoo', extensionInterface: SprinkleRecipe::class);
    }

    /**
     * We can now test getObjects.
     *
     * @depends testValidate
     */
    public function testGetObjects(): void
    {
        $ci = Mockery::mock(Container::class);

        $manager = Mockery::mock(SprinkleManager::class)
            ->shouldReceive('getSprinkles')->once()->andReturn([SprinkleStub::class])
            ->getMock();

        $loader = new RecipeExtensionLoader($manager, $ci);

        $instances = $loader->getObjects('getBar');

        $this->assertIsArray($instances);
        $this->assertCount(1, $instances);
        $this->assertInstanceOf(RecipeExtensionLoaderStub::class, $instances[0]);
    }

    /**
     * We can now test getObjects.
     *
     * @depends testGetObjects
     */
    public function testGetObjectsWithNoRecipe(): void
    {
        $ci = Mockery::mock(Container::class);

        $manager = Mockery::mock(SprinkleManager::class)
            ->shouldReceive('getSprinkles')->once()->andReturn([SprinkleStubB::class])
            ->getMock();

        $loader = new RecipeExtensionLoader($manager, $ci);

        $instances = $loader->getObjects('getBar', CustomRecipeInterface::class);

        $this->assertIsArray($instances);
        $this->assertCount(0, $instances);
    }

    /**
     * We can now test getObjects.
     *
     * @depends testGetObjects
     */
    public function testGetObjectsWithTwoSprinkles(): void
    {
        $ci = Mockery::mock(Container::class);

        $manager = Mockery::mock(SprinkleManager::class)
            ->shouldReceive('getSprinkles')->once()->andReturn([SprinkleStub::class, SprinkleStubB::class])
            ->getMock();

        $loader = new RecipeExtensionLoader($manager, $ci);

        $instances = $loader->getObjects('getBar', CustomRecipeInterface::class);

        $this->assertIsArray($instances);
        $this->assertCount(1, $instances);
        $this->assertInstanceOf(RecipeExtensionLoaderStub::class, $instances[0]);
    }

    /**
     * Make sure recipeInterface is correctly passed.
     *
     * @depends testValidateWithBadInterface
     */
    public function testGetObjectsWithBadRecipeInterfaceWithThrowInterface(): void
    {
        $ci = Mockery::mock(Container::class);

        $manager = Mockery::mock(SprinkleManager::class)
            ->shouldReceive('getSprinkles')->once()->andReturn([SprinkleStub::class])
            ->getMock();

        $loader = new RecipeExtensionLoader($manager, $ci);

        $this->expectException(BadInstanceOfException::class);
        $loader->getObjects('getBar', recipeInterface: ContainerInterface::class, throwBadInterface: true);
    }

    /**
     * Make sure extensionInterface is correctly passed.
     *
     * @depends testValidateWithBadInterface
     */
    public function testGetObjectsWithBadExtensionInterface(): void
    {
        $ci = Mockery::mock(Container::class);

        $manager = Mockery::mock(SprinkleManager::class)
            ->shouldReceive('getSprinkles')->once()->andReturn([SprinkleStub::class])
            ->getMock();

        $loader = new RecipeExtensionLoader($manager, $ci);

        $this->expectException(BadInstanceOfException::class);
        $loader->getObjects('getBar', extensionInterface: SprinkleRecipe::class);
    }
}

class RecipeExtensionLoaderStub
{
}

class RecipeExtensionLoaderStubExtended extends RecipeExtensionLoaderStub
{
}

interface CustomRecipeInterface
{
}

class SprinkleStub extends TestSprinkle implements CustomRecipeInterface
{
    public static function getFoo(): array
    {
        return [
            RecipeExtensionLoaderStub::class
        ];
    }

    public static function getBar(): array
    {
        return [
            new RecipeExtensionLoaderStub(),
        ];
    }
}

class SprinkleStubB extends TestSprinkle
{
}
