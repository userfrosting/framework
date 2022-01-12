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
use InvalidArgumentException;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use UserFrosting\Sprinkle\RecipeExtensionLoader;
use UserFrosting\Sprinkle\SprinkleManager;
use UserFrosting\Sprinkle\SprinkleRecipe;
use UserFrosting\Support\Exception\BadClassNameException;
use UserFrosting\Support\Exception\BadInstanceOfException;
use UserFrosting\Support\Exception\BadMethodNameException;
use UserFrosting\Tests\TestSprinkle\TestSprinkle;

class RecipeExtensionLoaderTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * Purpose of this test is to create a reusable $loader instance so the
     * same code is not reused too much.
     */
    protected function getLoader(): RecipeExtensionLoader
    {
        $ci = Mockery::mock(Container::class);
        $manager = Mockery::mock(SprinkleManager::class);

        $loader = new RecipeExtensionLoader($manager, $ci);

        return $loader;
    }

    public function testValidate(): void
    {
        $loader = $this->getLoader();
        $this->assertTrue($loader->validateClass(RecipeExtensionLoaderStub::class));
    }

    public function testValidateWithInterface(): void
    {
        $loader = $this->getLoader();
        $isValid = $loader->validateClass(SprinkleStub::class, SprinkleRecipe::class);
        $this->assertTrue($isValid);
    }

    public function testValidateWithInterfaceAndObject(): void
    {
        $loader = $this->getLoader();
        $isValid = $loader->validateClass(new SprinkleStub(), SprinkleRecipe::class);
        $this->assertTrue($isValid);
    }

    public function testValidateWithSubclass(): void
    {
        $loader = $this->getLoader();
        $isValid = $loader->validateClass(RecipeExtensionLoaderStubExtended::class, RecipeExtensionLoaderStub::class);
        $this->assertTrue($isValid);
    }

    public function testValidateWithBadSubclass(): void
    {
        $loader = $this->getLoader();
        $result = $loader->validateClass(\stdClass::class, RecipeExtensionLoaderStub::class);
        $this->assertFalse($result);
    }

    public function testValidateWithBadSubclassWithThrowInterface(): void
    {
        $loader = $this->getLoader();
        $this->expectException(BadInstanceOfException::class);
        $loader->validateClass(\stdClass::class, RecipeExtensionLoaderStub::class, true);
    }

    public function testValidateClassNotFound(): void
    {
        $loader = $this->getLoader();
        $this->expectException(BadClassNameException::class);
        // @phpstan-ignore-next-line
        $loader->validateClass(Bar::class);
    }

    public function testValidateWithBadInterface(): void
    {
        $loader = $this->getLoader();
        $result = $loader->validateClass(SprinkleStub::class, ContainerInterface::class);
        $this->assertFalse($result);
    }

    public function testValidateWithBadInterfaceWithThrowInterface(): void
    {
        $loader = $this->getLoader();
        $this->expectException(BadInstanceOfException::class);
        $loader->validateClass(new SprinkleStub(), ContainerInterface::class, true);
    }

    /**
     * We can now test getInstances.
     *
     * @depends testValidate
     */
    public function testGetInstances(): void
    {
        /** @var Container */
        $ci = Mockery::mock(Container::class)
            ->shouldReceive('get')
            ->with(RecipeExtensionLoaderStub::class)
            ->once()
            ->andReturn(new RecipeExtensionLoaderStub())
            ->getMock();

        /** @var SprinkleManager */
        $manager = Mockery::mock(SprinkleManager::class)
            ->shouldReceive('getSprinkles')->once()->andReturn([new SprinkleStub()])
            ->getMock();

        $loader = new RecipeExtensionLoader($manager, $ci);

        $instances = $loader->getInstances('getFoo');

        $this->assertCount(1, $instances);
        $this->assertInstanceOf(RecipeExtensionLoaderStub::class, $instances[0]);
    }

    /**
     * Test when method doesn't exist.
     *
     * @depends testGetInstances
     */
    public function testGetInstancesWithBadMethod(): void
    {
        /** @var Container */
        $ci = Mockery::mock(Container::class)
            ->shouldNotReceive('get')
            ->getMock();

        /** @var SprinkleManager */
        $manager = Mockery::mock(SprinkleManager::class)
            ->shouldReceive('getSprinkles')->once()->andReturn([new SprinkleStub()])
            ->getMock();

        $loader = new RecipeExtensionLoader($manager, $ci);

        $instances = $loader->getInstances('getFooBar');

        $this->assertCount(0, $instances);
    }

    /**
     * @depends testGetInstancesWithBadMethod
     */
    public function testGetInstancesWithBadMethodWithThrownException(): void
    {
        /** @var Container */
        $ci = Mockery::mock(Container::class)
            ->shouldNotReceive('get')
            ->getMock();

        /** @var SprinkleManager */
        $manager = Mockery::mock(SprinkleManager::class)
            ->shouldReceive('getSprinkles')->once()->andReturn([new SprinkleStub()])
            ->getMock();

        $loader = new RecipeExtensionLoader($manager, $ci);

        $this->expectException(BadMethodNameException::class);
        $loader->getInstances('getFooBar', throwBadInterface: true);
    }

    /**
     * Test when method doesn't return iterable.
     *
     * @depends testGetInstances
     */
    public function testGetInstancesWithNonIterable(): void
    {
        /** @var Container */
        $ci = Mockery::mock(Container::class)
            ->shouldNotReceive('get')
            ->getMock();

        /** @var SprinkleManager */
        $manager = Mockery::mock(SprinkleManager::class)
            ->shouldReceive('getSprinkles')->once()->andReturn([new SprinkleStub()])
            ->getMock();

        $loader = new RecipeExtensionLoader($manager, $ci);

        $instances = $loader->getInstances('getNonArray');

        $this->assertCount(0, $instances);
    }

    /**
     * Test when method doesn't exist.
     *
     * @depends testGetInstancesWithNonIterable
     */
    public function testGetInstancesWithNonIterableWithThrownException(): void
    {
        /** @var Container */
        $ci = Mockery::mock(Container::class)
            ->shouldNotReceive('get')
            ->getMock();

        /** @var SprinkleManager */
        $manager = Mockery::mock(SprinkleManager::class)
            ->shouldReceive('getSprinkles')->once()->andReturn([new SprinkleStub()])
            ->getMock();

        $loader = new RecipeExtensionLoader($manager, $ci);

        $this->expectException(InvalidArgumentException::class);
        $loader->getInstances('getNonArray', throwBadInterface: true);
    }

    /**
     * We can now test getInstances.
     *
     * @depends testGetInstances
     */
    public function testGetInstancesWithNoRecipe(): void
    {
        /** @var Container */
        $ci = Mockery::mock(Container::class)
            ->shouldNotReceive('get')->with(RecipeExtensionLoaderStub::class)
            ->getMock();

        /** @var SprinkleManager */
        $manager = Mockery::mock(SprinkleManager::class)
            ->shouldReceive('getSprinkles')->once()->andReturn([SprinkleStubB::class])
            ->getMock();

        $loader = new RecipeExtensionLoader($manager, $ci);

        $instances = $loader->getInstances('getFoo', CustomRecipeInterface::class);

        $this->assertCount(0, $instances);
    }

    /**
     * We can now test getInstances.
     *
     * @depends testGetInstances
     */
    public function testGetInstancesWithTwoSprinkles(): void
    {
        /** @var Container */
        $ci = Mockery::mock(Container::class)
            ->shouldReceive('get')
            ->with(RecipeExtensionLoaderStub::class)
            ->once()
            ->andReturn(new RecipeExtensionLoaderStub())
            ->getMock();

        /** @var SprinkleManager */
        $manager = Mockery::mock(SprinkleManager::class)
            ->shouldReceive('getSprinkles')->once()->andReturn([
                new SprinkleStub(),
                new SprinkleStubB()
            ])->getMock();

        $loader = new RecipeExtensionLoader($manager, $ci);

        $instances = $loader->getInstances('getFoo', CustomRecipeInterface::class);

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
        /** @var Container */
        $ci = Mockery::mock(Container::class);

        /** @var SprinkleManager */
        $manager = Mockery::mock(SprinkleManager::class)
            ->shouldReceive('getSprinkles')->once()->andReturn([new SprinkleStub()])
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
        /** @var Container */
        $ci = Mockery::mock(Container::class);

        /** @var SprinkleManager */
        $manager = Mockery::mock(SprinkleManager::class)
            ->shouldReceive('getSprinkles')->once()->andReturn([new SprinkleStub()])
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
        /** @var Container */
        $ci = Mockery::mock(Container::class);

        /** @var SprinkleManager */
        $manager = Mockery::mock(SprinkleManager::class)
            ->shouldReceive('getSprinkles')->once()->andReturn([new SprinkleStub()])
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
        /** @var Container */
        $ci = Mockery::mock(Container::class);

        /** @var SprinkleManager */
        $manager = Mockery::mock(SprinkleManager::class)
            ->shouldReceive('getSprinkles')->once()->andReturn([new SprinkleStub()])
            ->getMock();

        $loader = new RecipeExtensionLoader($manager, $ci);

        $instances = $loader->getObjects('getBar');

        $this->assertCount(1, $instances);
        $this->assertInstanceOf(RecipeExtensionLoaderStub::class, $instances[0]);
    }

    /**
     * @depends testGetObjects
     */
    public function testGetObjectsWithBadMethod(): void
    {
        /** @var Container */
        $ci = Mockery::mock(Container::class);

        /** @var SprinkleManager */
        $manager = Mockery::mock(SprinkleManager::class)
            ->shouldReceive('getSprinkles')->once()->andReturn([new SprinkleStub()])
            ->getMock();

        $loader = new RecipeExtensionLoader($manager, $ci);

        $instances = $loader->getObjects('getFooBar');

        $this->assertCount(0, $instances);
    }

    /**
     * @depends testGetObjectsWithBadMethod
     */
    public function testGetObjectsWithBadMethodWithThrownException(): void
    {
        /** @var Container */
        $ci = Mockery::mock(Container::class);

        /** @var SprinkleManager */
        $manager = Mockery::mock(SprinkleManager::class)
            ->shouldReceive('getSprinkles')->once()->andReturn([new SprinkleStub()])
            ->getMock();

        $loader = new RecipeExtensionLoader($manager, $ci);

        $this->expectException(BadMethodNameException::class);
        $loader->getObjects('getFooBar', throwBadInterface: true);
    }

    /**
     * We can now test getObjects.
     *
     * @depends testGetObjects
     */
    public function testGetObjectsWithNoRecipe(): void
    {
        /** @var Container */
        $ci = Mockery::mock(Container::class);

        /** @var SprinkleManager */
        $manager = Mockery::mock(SprinkleManager::class)
            ->shouldReceive('getSprinkles')->once()->andReturn([new SprinkleStubB()])
            ->getMock();

        $loader = new RecipeExtensionLoader($manager, $ci);

        $instances = $loader->getObjects('getBar', CustomRecipeInterface::class);

        $this->assertCount(0, $instances);
    }

    /**
     * We can now test getObjects.
     *
     * @depends testGetObjects
     */
    public function testGetObjectsWithTwoSprinkles(): void
    {
        /** @var Container */
        $ci = Mockery::mock(Container::class);

        /** @var SprinkleManager */
        $manager = Mockery::mock(SprinkleManager::class)
            ->shouldReceive('getSprinkles')->once()->andReturn([
                new SprinkleStub(),
                new SprinkleStubB()
            ])->getMock();

        $loader = new RecipeExtensionLoader($manager, $ci);

        $instances = $loader->getObjects('getBar', CustomRecipeInterface::class);

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
        /** @var Container */
        $ci = Mockery::mock(Container::class);

        /** @var SprinkleManager */
        $manager = Mockery::mock(SprinkleManager::class)
            ->shouldReceive('getSprinkles')->once()->andReturn([new SprinkleStub()])
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
        /** @var Container */
        $ci = Mockery::mock(Container::class);

        /** @var SprinkleManager */
        $manager = Mockery::mock(SprinkleManager::class)
            ->shouldReceive('getSprinkles')->once()->andReturn([new SprinkleStub()])
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
    /** @return string[] */
    public function getFoo(): array
    {
        return [
            RecipeExtensionLoaderStub::class
        ];
    }

    /** @return object[] */
    public function getBar(): array
    {
        return [
            new RecipeExtensionLoaderStub(),
        ];
    }

    /** @return string */
    public function getNonArray(): string
    {
        return 'fooBar';
    }
}

class SprinkleStubB extends TestSprinkle
{
}
