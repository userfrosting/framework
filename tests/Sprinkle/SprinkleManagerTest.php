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
use UserFrosting\Sprinkle\SprinkleManager;
use UserFrosting\Support\Exception\BadClassNameException;
use UserFrosting\Support\Exception\BadInstanceOfException;
use UserFrosting\Tests\TestSprinkle\TestServicesProviders;
use UserFrosting\Tests\TestSprinkle\TestSprinkle;

class SprinkleManagerTest extends TestCase
{
    /**
     * getSprinkles|getMainSprinkle
     */
    public function testGetSprinklesWithNoDependent(): void
    {
        $manager = new SprinkleManager(CoreStub::class);
        $sprinkles = $manager->getSprinkles();

        $this->assertCount(1, $sprinkles);
        $this->assertContainsOnlyInstancesOf(CoreStub::class, $sprinkles);
        $this->assertSame('Test Sprinkle', $sprinkles[0]->getName());

        // Test getMainSprinkle while at it
        $this->assertInstanceOf(CoreStub::class, $manager->getMainSprinkle());
        $this->assertSame('Test Sprinkle', $manager->getMainSprinkle()->getName());
    }

    /**
     * @depends testGetSprinklesWithNoDependent
     */
    public function testGetSprinklesWithNoDependentAndObject(): void
    {
        $manager = new SprinkleManager(new CoreStub());
        $sprinkles = $manager->getSprinkles();

        $this->assertCount(1, $sprinkles);
        $this->assertContainsOnlyInstancesOf(CoreStub::class, $sprinkles);
    }

    /**
     * @depends testGetSprinklesWithNoDependent
     */
    public function testGetSprinklesWithManyDependent(): void
    {
        $manager = new SprinkleManager(MainStub::class);
        $sprinkles = $manager->getSprinkles();

        // TODO : Change to key array comparison once enabled
        $this->assertCount(4, $sprinkles);
        $this->assertInstanceOf(CoreStub::class, $sprinkles[0]);
        $this->assertInstanceOf(AdminStub::class, $sprinkles[1]);
        $this->assertInstanceOf(AccountStub::class, $sprinkles[2]);
        $this->assertInstanceOf(MainStub::class, $sprinkles[3]);
        $this->assertInstanceOf(MainStub::class, $manager->getMainSprinkle());

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
        $sprinkles = $manager->getSprinkles();

        // TODO : Change to key array comparison once enabled
        $this->assertCount(4, $sprinkles);
        $this->assertInstanceOf(CoreStub::class, $sprinkles[0]);
        $this->assertInstanceOf(AccountStub::class, $sprinkles[1]);
        $this->assertInstanceOf(AdminNestedStub::class, $sprinkles[2]);
        $this->assertInstanceOf(MainNestedStub::class, $sprinkles[3]);
    }

    /**
     * @depends testGetSprinklesWithNestedDependent
     */
    // TODO : Make list <:class => instance>
    /*public function testGetSprinklesWithDuplicateSprinkles(): void
    {
        $manager = new SprinkleManager(MainDuplicateStub::class);
        $sprinkles = $manager->getSprinkles();

        // TODO : Change to key array comparison once enabled
        $this->assertCount(4, $sprinkles);
        $this->assertInstanceOf(CoreStub::class, $sprinkles[0]);
        $this->assertInstanceOf(AccountStub::class, $sprinkles[1]);
        $this->assertInstanceOf(AdminNestedStub::class, $sprinkles[2]);
        $this->assertInstanceOf(MainDuplicateStub::class, $sprinkles[3]);
    }*/

    /**
     * __construct & Exceptions
     */
    public function testConstructorWithBadSprinkleClass(): void
    {
        $this->expectException(BadInstanceOfException::class);
        $manager = new SprinkleManager(\stdClass::class);
    }

    public function testConstructorWithNonExistingClass(): void
    {
        $this->expectException(BadClassNameException::class);

        // @phpstan-ignore-next-line
        $manager = new SprinkleManager(FooBar::class);
    }

    /**
     * @depends testConstructorWithBadSprinkleClass
     * @depends testGetSprinklesWithManyDependent
     */
    public function testConstructorWithBadSprinkleClassInDependent(): void
    {
        $this->expectException(BadInstanceOfException::class);
        $manager = new SprinkleManager(NotSprinkleStub::class);
    }

    /**
     * Test Services
     */
    public function testGetServicesDefinitions(): void
    {
        $manager = new SprinkleManager(TestSprinkle::class);
        $services = $manager->getServicesDefinitions();

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

    /**
     * @depends testGetServicesDefinitions
     */
    public function testGetServicesDefinitionsWithNotFound(): void
    {
        $manager = new SprinkleManager(NotFoundStub::class);
        $this->expectException(BadClassNameException::class);
        $manager->getServicesDefinitions();
    }

    /**
     * @depends testGetServicesDefinitions
     */
    public function testGetServicesDefinitionsWithObject(): void
    {
        $manager = new SprinkleManager(ServiceSprinkleStub::class);
        $services = $manager->getServicesDefinitions();

        $this->assertCount(1, $services);
        $this->assertArrayHasKey('testMessageGenerator', $services);
    }
}

class CoreStub extends TestSprinkle
{
}

class AdminStub extends TestSprinkle
{
    public function getBakeryCommands(): array
    {
        return [
            CommandStub::class,
        ];
    }
}

class AdminNestedStub extends TestSprinkle
{
    public function getSprinkles(): array
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
    public function getName(): string
    {
        return 'Main Sprinkle';
    }

    public function getSprinkles(): array
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
    public function getSprinkles(): array
    {
        return [
            CoreStub::class,
            AdminNestedStub::class,
        ];
    }
}

class MainDuplicateStub extends TestSprinkle
{
    public function getSprinkles(): array
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
    public function getSprinkles(): array
    {
        return [\stdClass::class];
    }
}

class BadInstanceOfSprinkleStub extends TestSprinkle
{
    public function getRoutes(): array
    {
        return [\stdClass::class];
    }

    public function getServices(): array
    {
        return [\stdClass::class];
    }

    public function getBakeryCommands(): array
    {
        return [\stdClass::class];
    }

    public function getMiddlewares(): array
    {
        return [\stdClass::class];
    }
}

class NotFoundStub extends TestSprinkle
{
    public function getServices(): array
    {
        // @phpstan-ignore-next-line
        return [NotFound::class];
    }

    public function getBakeryCommands(): array
    {
        // @phpstan-ignore-next-line
        return [NotFound::class];
    }

    public function getMiddlewares(): array
    {
        // @phpstan-ignore-next-line
        return [NotFound::class];
    }
}

class ServiceSprinkleStub extends TestSprinkle
{
    public function getServices(): array
    {
        return [
            new TestServicesProviders(),
        ];
    }
}

class CommandStub extends Command
{
    // @phpstan-ignore-next-line
    protected function configure()
    {
        $this->setName('stub');
    }
}
