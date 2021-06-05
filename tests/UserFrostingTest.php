<?php

/*
 * UserFrosting Framework (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/framework
 * @copyright Copyright (c) 2013-2021 Alexander Weissman, Louis Charette, Jordan Mele
 * @license   https://github.com/userfrosting/framework/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Tests;

use Psr\Container\ContainerInterface;
use Slim\App;
use UserFrosting\Sprinkle\SprinkleManager;
use UserFrosting\Testing\TestCase;
use UserFrosting\Tests\TestSprinkle\TestSprinkle;
use UserFrosting\UserFrosting;

/**
 * Tests UserFrosting class.
 */
class UserFrostingTest extends TestCase
{
    protected string $mainSprinkle = TestSprinkle::class;

    public function testConstructor(): UserFrosting
    {
        $this->assertInstanceOf(UserFrosting::class, $this->userfrosting);

        return $this->userfrosting;
    }

    /**
     * @depends testConstructor
     */
    public function testGetters(UserFrosting $userfrosting): void
    {
        $this->assertInstanceOf(App::class, $userfrosting->getApp());
        $this->assertInstanceOf(ContainerInterface::class, $userfrosting->getContainer());
        $this->assertSame(TestSprinkle::class, $userfrosting->getMainSprinkle());
        $this->assertInstanceOf(SprinkleManager::class, $userfrosting->getContainer()->get(SprinkleManager::class));
    }

    /**
     * Test a basic Hello World Page
     * @depends testConstructor
     */
    public function testFullRoute(UserFrosting $userfrosting): void
    {
        // Create request with method and url and fetch response
        $request = $this->createRequest('GET', '/foo');
        $response = $this->handleRequest($request);

        // Asserts
        $this->assertSame(200, $response->getStatusCode());
        $this->assertResponseJson(['Great work! Keep going!', 'Great work! Keep going!', 'bar'], $response);
    }
}
