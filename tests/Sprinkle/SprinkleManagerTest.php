<?php

/*
 * UserFrosting Framework (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/framework
 * @copyright Copyright (c) 2013-2021 Alexander Weissman, Louis Charette, Jordan Mele
 * @license   https://github.com/userfrosting/framework/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Tests\Unit;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use UserFrosting\Sprinkle\SprinkleManager;
use UserFrosting\Sprinkle\SprinkleReceipe;
use UserFrosting\Tests\TestSprinkle;

class SprinkleManagerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testGetSprinklesWithNoDependent(): void
    {
        $mockSprinkle = m::mock(SprinkleReceipe::class);
        $mockSprinkle->shouldReceive('getSprinkles')->once()->andReturn([]);

        $manager = new SprinkleManager($mockSprinkle);
        $manager->loadSprinkles();

        // Assertions
        $this->assertInstanceOf(SprinkleManager::class, $manager);
        $this->assertSame([$mockSprinkle], $manager->getSprinkles());
    }

    /**
     * @depends testGetSprinklesWithNoDependent
     */
    public function testBadClassSprinkleAsDependent(): void
    {
        $manager = new SprinkleManager(new NotSprinkleStub());
        $this->expectException(\TypeError::class);
        $manager->loadSprinkles();
    }

    /**
     * @depends testGetSprinklesWithNoDependent
     */
    public function testGetSprinklesWithManyDependent(): void
    {
        $manager = new SprinkleManager(new MainStub());
        $manager->loadSprinkles();

        // Assert getSprinkles
        $this->assertCount(4, $manager->getSprinkles());
    }

    /**
     * @depends testGetSprinklesWithManyDependent
     */
    public function testGetSprinklesWithManyMockedDependent(): void
    {
        // Create 4 mock sprinkles
        $mainSprinkle = m::mock(SprinkleReceipe::class);
        $coreSprinkle = m::mock(SprinkleReceipe::class);
        $accountSprinkle = m::mock(SprinkleReceipe::class);
        $adminSprinkle = m::mock(SprinkleReceipe::class);

        // Add dependencies to sprinkles
        $mainSprinkle->shouldReceive('getSprinkles')->once()->andReturn([
            $coreSprinkle,
            $adminSprinkle,
            $accountSprinkle,
        ]);
        $coreSprinkle->shouldReceive('getSprinkles')->andReturn([]);
        $accountSprinkle->shouldReceive('getSprinkles')->andReturn([]);
        $adminSprinkle->shouldReceive('getSprinkles')->andReturn([]);

        $manager = new SprinkleManager($mainSprinkle);
        $manager->loadSprinkles();

        // Assert getSprinkles
        $sprinkles = $manager->getSprinkles();
        $this->assertCount(4, $sprinkles);
        $this->assertSame([$mainSprinkle, $coreSprinkle, $adminSprinkle, $accountSprinkle], $sprinkles);
    }

    /**
     * @depends testGetSprinklesWithManyDependent
     */
    public function testGetSprinklesWithNestedDependent(): void
    {
        $manager = new SprinkleManager(new MainNestedStub());
        $manager->loadSprinkles();

        // Assert getSprinkles
        $this->assertCount(4, $manager->getSprinkles());
    }

    public function testGetSprinklesWithNestedMockDependent(): void
    {
        // Create 4 mock sprinkles
        $mainSprinkle = m::mock(SprinkleReceipe::class);
        $coreSprinkle = m::mock(SprinkleReceipe::class);
        $accountSprinkle = m::mock(SprinkleReceipe::class);
        $adminSprinkle = m::mock(SprinkleReceipe::class);

        // Add dependencies to sprinkles
        $adminSprinkle->shouldReceive('getSprinkles')->once()->andReturn([
            $accountSprinkle,
        ]);
        $mainSprinkle->shouldReceive('getSprinkles')->once()->andReturn([
            $coreSprinkle,
            $adminSprinkle,
        ]);
        $coreSprinkle->shouldReceive('getSprinkles')->andReturn([]);
        $accountSprinkle->shouldReceive('getSprinkles')->andReturn([]);

        $manager = new SprinkleManager($mainSprinkle);
        $manager->loadSprinkles();

        // Assert getSprinkles
        $sprinkles = $manager->getSprinkles();
        $this->assertCount(4, $sprinkles);
        $this->assertSame([$mainSprinkle, $coreSprinkle, $adminSprinkle, $accountSprinkle], $sprinkles);
    }

    public function testGetBakeryCommandsWhenEmpty(): void
    {
        $mockSprinkle = m::mock(SprinkleReceipe::class);
        $mockSprinkle->shouldReceive('getSprinkles')->once()->andReturn([]);
        $mockSprinkle->shouldReceive('getBakeryCommands')->once()->andReturn([]);

        $manager = new SprinkleManager($mockSprinkle);
        $manager->loadSprinkles();

        // Assertions
        $this->assertSame([], $manager->getBakeryCommands());
    }

    /**
     * @depends testGetBakeryCommandsWhenEmpty
     */
    public function testGetBakeryCommands(): void
    {
        $mockCommand = m::mock(Command::class);

        $mainSprinkle = m::mock(SprinkleReceipe::class);
        $coreSprinkle = m::mock(SprinkleReceipe::class);

        $coreSprinkle->shouldReceive('getSprinkles')->andReturn([]);
        $mainSprinkle->shouldReceive('getSprinkles')->once()->andReturn([
            $coreSprinkle,
        ]);
        $coreSprinkle->shouldReceive('getBakeryCommands')->once()->andReturn([$mockCommand]);
        $mainSprinkle->shouldReceive('getBakeryCommands')->once()->andReturn([]);

        $manager = new SprinkleManager($mainSprinkle);
        $manager->loadSprinkles();

        // Assertions
        $this->assertSame([$mockCommand], $manager->getBakeryCommands());
    }

    /**
     * N.B.: Command interface will be checked in Bakery, not SprinkleManager.
     * @depends testGetBakeryCommands
     */
    public function testGetBakeryCommandsWithBadCommand(): void
    {
        $mockCommand = new \stdClass();
        $mainSprinkle = m::mock(SprinkleReceipe::class);
        $mainSprinkle->shouldReceive('getSprinkles')->once()->andReturn([]);
        $mainSprinkle->shouldReceive('getBakeryCommands')->once()->andReturn([$mockCommand]);

        $manager = new SprinkleManager($mainSprinkle);
        $manager->loadSprinkles();

        // Assertions
        $this->assertSame([$mockCommand], $manager->getBakeryCommands());
    }
}

class CoreStub extends TestSprinkle
{
}

class AdminStub extends TestSprinkle
{
}

class AdminNestedStub extends TestSprinkle
{
    public function getSprinkles(): array
    {
        return [
            new AccountStub(),
        ];
    }
}

class AccountStub extends TestSprinkle
{
}

class MainStub extends TestSprinkle
{
    public function getSprinkles(): array
    {
        return [
            new CoreStub(),
            new AdminStub(),
            new AccountStub(),
        ];
    }
}

class MainNestedStub extends TestSprinkle
{
    public function getSprinkles(): array
    {
        return [
            new CoreStub(),
            new AdminNestedStub(),
        ];
    }
}

class NotSprinkleStub extends TestSprinkle
{
    public function getSprinkles(): array
    {
        return [
            new \stdClass(),
        ];
    }
}
