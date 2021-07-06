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
use UserFrosting\Support\Exception\NotFoundException;
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
        $this->assertTrue($loader->validateClass(Foo::class));
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
    public function testValidateClassNotFound(RecipeExtensionLoader $loader): void
    {
        $this->expectException(NotFoundException::class);
        $loader->validateClass(Bar::class);
    }

    /**
     * @depends testConstructor
     *
     * @param RecipeExtensionLoader $loader
     */
    public function testValidateWithBadInterface(RecipeExtensionLoader $loader): void
    {
        $this->expectException(BadInstanceOfException::class);
        $loader->validateClass(SprinkleStub::class, ContainerInterface::class);
    }

    /**
     * We can now test getInstances.
     *
     * @depends testValidate
     */
    public function testGetInstances(): void
    {
        $ci = Mockery::mock(Container::class)
            ->shouldReceive('get')->with(Foo::class)->once()->andReturn(Foo::class)
            ->getMock();

        $manager = Mockery::mock(SprinkleManager::class)
            ->shouldReceive('getSprinkles')->once()->andReturn([SprinkleStub::class])
            ->getMock();

        $loader = new RecipeExtensionLoader($manager, $ci);

        $instances = $loader->getInstances('getFoo');

        $this->assertSame($instances, [Foo::class]);
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

        $this->expectException(BadInstanceOfException::class);
        $loader->getInstances('getFoo', recipeInterface: ContainerInterface::class);
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
}

class Foo
{
}

class SprinkleStub extends TestSprinkle
{
    public static function getFoo(): array
    {
        return [
            Foo::class
        ];
    }
}
