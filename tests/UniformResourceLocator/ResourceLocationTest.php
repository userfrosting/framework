<?php

/*
 * UserFrosting Framework (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/framework
 * @copyright Copyright (c) 2013-2021 Alexander Weissman, Louis Charette, Jordan Mele
 * @license   https://github.com/userfrosting/framework/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Tests\UniformResourceLocator;

use PHPUnit\Framework\TestCase;
use UserFrosting\UniformResourceLocator\ResourceLocation;
use UserFrosting\UniformResourceLocator\ResourceLocationInterface;

/**
 * Tests for ResourceLocator.
 */
class ResourceLocationTest extends TestCase
{
    /**
     * Test ResourceLocation class.
     */
    public function testResourceLocation(): void
    {
        // Test instance & default values
        $location = new ResourceLocation('');
        $this->assertInstanceOf(ResourceLocationInterface::class, $location);
        $this->assertEquals('', $location->getName());
        $this->assertEquals('', $location->getPath());

        // Set/get name & path properties
        $location->setName('foo');
        $this->assertEquals('foo', $location->getName());

        $location->setPath('/bar');
        $this->assertEquals('/bar/', $location->getPath());
    }

    /**
     * Now try again with the info in the constructor.
     */
    public function testResourceLocation_ctor(): void
    {
        $location = new ResourceLocation('bar', '/foo');
        $this->assertEquals('bar', $location->getName());
        $this->assertEquals('/foo/', $location->getPath());
    }

    /**
     * @depends testResourceLocation_ctor
     */
    public function testResourceLocation_ctorWithSupressesRightSlashe(): void
    {
        $location = new ResourceLocation('bar', '/foo/');
        $this->assertEquals('bar', $location->getName());
        $this->assertEquals('/foo/', $location->getPath());
    }

    /**
     * @depends testResourceLocation_ctor
     */
    public function testResourceLocation_ctoOmittedPathEqualsName(): void
    {
        $location = new ResourceLocation('bar');
        $this->assertEquals('bar', $location->getName());
        $this->assertEquals('bar/', $location->getPath());
    }
}
