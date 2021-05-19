<?php

/*
 * UserFrosting Framework (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/framework
 * @copyright Copyright (c) 2013-2021 Alexander Weissman, Louis Charette, Jordan Mele
 * @license   https://github.com/userfrosting/framework/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Tests;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Slim\App;
use UserFrosting\Sprinkle\SprinkleManager;
use UserFrosting\Tests\TestSprinkle\TestSprinkle;
use UserFrosting\UserFrosting;

/**
 * Tests UserFrosting class.
 */
class UserFrostingTest extends TestCase
{
    use HttpTester;

    public function testConstructor(): UserFrosting
    {
        $userfrosting = new UserFrosting(TestSprinkle::class);
        $this->assertInstanceOf(UserFrosting::class, $userfrosting);

        return $userfrosting;
    }

    /**
     * @depends testConstructor
     */
    public function testGetters(UserFrosting $userfrosting): void
    {
        $this->assertInstanceOf(App::class, $userfrosting->getApp());
        $this->assertInstanceOf(ContainerInterface::class, $userfrosting->getContainer());
        $this->assertSame(TestSprinkle::class, $userfrosting->getMainSprinkle());
        $this->assertInstanceOf(SprinkleManager::class, $userfrosting->getContainer()->get('sprinkleManager'));
    }

    /**
     * Test a basic Hello World Page
     * @depends testConstructor
     */
    public function testFullRoute(UserFrosting $userfrosting): void
    {
        $app = $userfrosting->getApp();

        // Create request with method and url
        $request = $this->createJsonRequest('GET', '/foo');

        // Make request and fetch response
        $response = $app->handle($request);

        // Asserts
        $this->assertSame(200, $response->getStatusCode());
        $this->assertResponseJson(["Great work! Keep going!","Great work! Keep going!"], $response);
    }
}
