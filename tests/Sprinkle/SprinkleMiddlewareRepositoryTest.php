<?php

/*
 * UserFrosting Framework (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/framework
 * @copyright Copyright (c) 2013-2021 Alexander Weissman, Louis Charette, Jordan Mele
 * @license   https://github.com/userfrosting/framework/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Tests\Sprinkle;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;
use stdClass;
use UserFrosting\Sprinkle\SprinkleManager;
use UserFrosting\Sprinkle\SprinkleMiddlewareRepository;
use UserFrosting\Sprinkle\SprinkleRecipe;
use UserFrosting\Support\Exception\BadClassNameException;
use UserFrosting\Support\Exception\BadInstanceOfException;

class SprinkleMiddlewareRepositoryTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testGetAll(): void
    {
        $middleware = Mockery::mock(MiddlewareInterface::class);

        /** @var SprinkleRecipe */
        $sprinkle = Mockery::mock(SprinkleRecipe::class)
            ->shouldReceive('getMiddlewares')->andReturn([$middleware::class])
            ->getMock();

        /** @var SprinkleManager */
        $sprinkleManager = Mockery::mock(SprinkleManager::class)
            ->shouldReceive('getSprinkles')->andReturn([$sprinkle])
            ->getMock();

        /** @var ContainerInterface */
        $ci = Mockery::mock(ContainerInterface::class)
            ->shouldReceive('get')->with($middleware::class)->andReturn($middleware)
            ->getMock();

        $repository = new SprinkleMiddlewareRepository($sprinkleManager, $ci);
        $classes = $repository->all();

        $this->assertCount(1, $classes);
        $this->assertSame($middleware, $classes[0]);
    }

    public function testGetAllWithCommandNotFound(): void
    {
        /** @var SprinkleRecipe */
        $sprinkle = Mockery::mock(SprinkleRecipe::class)
            ->shouldReceive('getMiddlewares')->andReturn(['/Not/MiddlewareInterface'])
            ->getMock();

        /** @var SprinkleManager */
        $sprinkleManager = Mockery::mock(SprinkleManager::class)
            ->shouldReceive('getSprinkles')->andReturn([$sprinkle])
            ->getMock();

        /** @var ContainerInterface */
        $ci = Mockery::mock(ContainerInterface::class);

        $repository = new SprinkleMiddlewareRepository($sprinkleManager, $ci);

        $this->expectException(BadClassNameException::class);
        $this->expectExceptionMessage('Middleware class `/Not/MiddlewareInterface` not found.');
        $repository->all();
    }

    public function testGetAllWithCommandWrongInterface(): void
    {
        $middleware = Mockery::mock(stdClass::class);

        /** @var SprinkleRecipe */
        $sprinkle = Mockery::mock(SprinkleRecipe::class)
            ->shouldReceive('getMiddlewares')->andReturn([$middleware::class])
            ->getMock();

        /** @var SprinkleManager */
        $sprinkleManager = Mockery::mock(SprinkleManager::class)
            ->shouldReceive('getSprinkles')->andReturn([$sprinkle])
            ->getMock();

        /** @var ContainerInterface */
        $ci = Mockery::mock(ContainerInterface::class)
            ->shouldReceive('get')->with($middleware::class)->andReturn($middleware)
            ->getMock();

        $repository = new SprinkleMiddlewareRepository($sprinkleManager, $ci);

        $this->expectException(BadInstanceOfException::class);
        $this->expectExceptionMessage('Middleware class `'.$middleware::class."` doesn't implement ".MiddlewareInterface::class.'.');
        $repository->all();
    }
}
