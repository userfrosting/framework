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
use UserFrosting\UserFrosting;

/**
 * Tests UserFrosting class.
 */
class UserFrostingTest extends TestCase
{
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
    }
}
