<?php

declare(strict_types=1);

/*
 * UserFrosting Framework (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/framework
 * @copyright Copyright (c) 2013-2021 Alexander Weissman, Louis Charette, Jordan Mele
 * @license   https://github.com/userfrosting/framework/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Tests\UniformResourceLocator;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use UserFrosting\UniformResourceLocator\Exception\LocationNotFoundException;
use UserFrosting\UniformResourceLocator\Exception\StreamNotFoundException;
use UserFrosting\UniformResourceLocator\Normalizer;
use UserFrosting\UniformResourceLocator\ResourceLocation;
use UserFrosting\UniformResourceLocator\ResourceLocationInterface;
use UserFrosting\UniformResourceLocator\ResourceLocator;
use UserFrosting\UniformResourceLocator\ResourceStream;
use UserFrosting\UniformResourceLocator\ResourceStreamInterface;
use UserFrosting\UniformResourceLocator\StreamWrapper\StreamBuilder;

/**
 * Tests for ResourceLocator.
 */
class ResourceLocatorTest extends TestCase
{
    public function testGetStreamBuilder(): void
    {
        $locator = new ResourceLocator();
        $streamBuilder = $locator->getStreamBuilder();
        $this->assertInstanceOf(StreamBuilder::class, $streamBuilder); // @phpstan-ignore-line
    }

    public function testGetBasePathWithEmptyConstructorArgument(): void
    {
        $locator = new ResourceLocator();
        $this->assertEquals('', $locator->getBasePath());
    }

    public function testSetBasePathWithConstructorArgument(): void
    {
        $locator = new ResourceLocator(__DIR__.'/Building');
        $this->assertEquals(Normalizer::normalizePath(__DIR__.'/Building'), $locator->getBasePath());
    }

    public function testAddStream(): void
    {
        $locator = new ResourceLocator();
        $this->assertFalse($locator->schemeExists('bar'));

        $stream = new ResourceStream('bar', 'foo');
        $locator->addStream($stream);

        $this->assertTrue($locator->schemeExists('bar'));

        $barStream = $locator->getStream('bar');
        $this->assertContainsOnlyInstancesOf(ResourceStreamInterface::class, $barStream);
    }

    public function testRegisterStream(): void
    {
        $locator = new ResourceLocator();
        $this->assertFalse($locator->schemeExists('bar'));

        $locator->registerStream('bar', 'foo');

        $this->assertTrue($locator->schemeExists('bar'));

        $barStream = $locator->getStream('bar');
        $this->assertContainsOnlyInstancesOf(ResourceStreamInterface::class, $barStream);
        $this->assertCount(1, $barStream);
        $this->assertEquals('foo/', $barStream[0]->getPath());
    }

    /**
     * @depends testRegisterStream
     */
    public function testRegisterSharedStream(): void
    {
        $locator = new ResourceLocator();
        $this->assertFalse($locator->schemeExists('bar'));

        $locator->registerStream('bar', 'foo', true);

        $this->assertTrue($locator->schemeExists('bar'));

        $barStream = $locator->getStream('bar');
        $this->assertContainsOnlyInstancesOf(ResourceStreamInterface::class, $barStream);
        $this->assertCount(1, $barStream);
        $this->assertEquals('foo/', $barStream[0]->getPath());
        $this->assertTrue($barStream[0]->isShared());
    }

    /**
     * @depends testRegisterSharedStream
     */
    public function testRegisterSharedStreamShort(): void
    {
        $locator = new ResourceLocator();
        $this->assertFalse($locator->schemeExists('bar'));

        $locator->registerSharedStream('bar', 'foo');

        $this->assertTrue($locator->schemeExists('bar'));

        $barStream = $locator->getStream('bar');
        $this->assertContainsOnlyInstancesOf(ResourceStreamInterface::class, $barStream);
        $this->assertCount(1, $barStream);
        $this->assertEquals('foo/', $barStream[0]->getPath());
        $this->assertTrue($barStream[0]->isShared());
    }

    /**
     * @depends testRegisterStream
     */
    public function testRegisterStreamWithOutPath(): void
    {
        $locator = new ResourceLocator();
        $this->assertFalse($locator->schemeExists('bar'));

        $locator->registerStream('bar');

        $this->assertTrue($locator->schemeExists('bar'));

        $barStream = $locator->getStream('bar');
        $this->assertContainsOnlyInstancesOf(ResourceStreamInterface::class, $barStream);
        $this->assertCount(1, $barStream);
        $this->assertEquals('bar/', $barStream[0]->getPath());
    }

    /**
     * @depends testRegisterStream
     */
    public function testStreamNotFoundException(): void
    {
        $locator = new ResourceLocator();
        $this->expectException(StreamNotFoundException::class);
        $locator->getStream('etc');
    }

    /**
     * @depends testRegisterStream
     */
    public function testAddStreamThrowExceptionOnRestrictedScheme(): void
    {
        $locator = new ResourceLocator();
        $this->expectException(InvalidArgumentException::class);
        $locator->registerStream('file');
    }

    /**
     * @depends testRegisterStream
     */
    public function testRemoveStream(): void
    {
        $locator = new ResourceLocator();
        $locator->registerStream('bar');
        $this->assertTrue($locator->schemeExists('bar'));
        $locator->removeStream('bar');
        $this->assertFalse($locator->schemeExists('bar'));
    }

    public function testGetStreams(): void
    {
        $locator = new ResourceLocator();
        $locator->registerStream('bar');
        $locator->registerStream('foo');

        $streams = $locator->getStreams();
        $this->assertCount(2, $streams);
        $this->assertContainsOnlyInstancesOf(ResourceStreamInterface::class, $streams['bar']);
        $this->assertCount(1, $streams['bar']);
        $this->assertEquals('bar/', $streams['bar'][0]->getPath());
    }

    public function testListStreams(): void
    {
        $locator = new ResourceLocator();
        $locator->registerStream('bar');
        $locator->registerStream('foo');

        $this->assertEquals(['bar', 'foo'], $locator->listStreams());
    }

    public function testIsStream(): void
    {
        $locator = new ResourceLocator();
        $locator->registerStream('foo');

        $this->assertFalse($locator->isStream('cars://foo.txt'));
        $this->assertTrue($locator->isStream('foo://cars'));
    }

    /**
     * @depends testIsStream
     */
    public function testIsStreamReturnFalseOnBadUri(): void
    {
        $locator = new ResourceLocator();
        $this->assertFalse($locator->isStream('path/to/../../../file.txt'));
    }

    public function testAddLocation(): void
    {
        $locator = new ResourceLocator();

        $location = new ResourceLocation('bar', '/foo');
        $locator->addLocation($location);

        $barLocation = $locator->getLocation('bar');
        $this->assertEquals('/foo/', $barLocation->getPath());
    }

    /**
     * @depends testAddLocation
     */
    public function testRegisterLocation(): void
    {
        $locator = new ResourceLocator();

        $locator->registerLocation('bar', '/foo');

        $barLocation = $locator->getLocation('bar');
        $this->assertEquals('/foo/', $barLocation->getPath());
    }

    /**
     * @depends testAddLocation
     */
    public function testRegisterLocationWithNoPath(): void
    {
        $locator = new ResourceLocator();

        $locator->registerLocation('blah');

        $barLocation = $locator->getLocation('blah');
        $this->assertEquals('blah/', $barLocation->getPath());
    }

    /**
     * @depends testAddLocation
     */
    public function testGetLocationThrowExceptionIfNotFound(): void
    {
        $locator = new ResourceLocator();
        $this->expectException(LocationNotFoundException::class);
        $locator->getLocation('etc');
    }

    /**
     * @depends testRegisterLocation
     */
    public function testGetLocations(): void
    {
        $locator = new ResourceLocator();
        $locator->registerLocation('bar', '/foo');
        $locator->registerLocation('foo', '/bar');

        $locations = $locator->getLocations();
        $this->assertCount(2, $locations);
        $this->assertContainsOnlyInstancesOf(ResourceLocationInterface::class, $locations);
        $this->assertEquals('/foo/', $locations['bar']->getPath());
    }

    /**
     * @depends testRegisterLocation
     */
    public function testListLocations(): void
    {
        $locator = new ResourceLocator();
        $locator->registerLocation('bar', '/foo');
        $locator->registerLocation('foo', '/bar/');

        // N.B.: Locations are list with the latest one (top priority) first
        $this->assertEquals(['foo', 'bar'], $locator->listLocations());
    }

    /**
     * @depends testRegisterLocation
     */
    public function testRemoveLocation(): void
    {
        $locator = new ResourceLocator();
        $locator->registerLocation('bar', '/foo/');
        $locator->registerLocation('foo', '/bar');

        $locator->removeLocation('bar');
        $this->assertCount(1, $locator->getLocations());
        $this->assertFalse($locator->locationExist('bar'));
        $this->assertTrue($locator->locationExist('foo'));
    }

    /**
     * @depends testGetLocations
     * @depends testGetStreams
     */
    public function testResourceLocatorReset(): void
    {
        $locator = new ResourceLocator();
        $locator->registerLocation('bar');
        $locator->registerLocation('foo');
        $locator->registerStream('bar');
        $locator->registerStream('foo');

        $this->assertCount(2, $locator->getStreams());
        $this->assertCount(2, $locator->getLocations());

        $locator->reset();

        $this->assertCount(0, $locator->getStreams());
        $this->assertCount(0, $locator->getLocations());
    }

    /**
     * Test issue for stream with empty path adding an extra `/`
     * Test for issue #16.
     */
    public function testStreamWithEmptyPath(): void
    {
        $locator = new ResourceLocator(__DIR__);
        $locator->registerStream('sprinkles', '');
        $locator->registerLocation('uploads', 'app/uploads/profile');

        $result = $locator->findResource('sprinkles://header.json', false);

        //NB.: __DIR__ doesn't end with a '/'.
        $this->assertSame('app/uploads/profile/header.json', $result);
    }

    /**
     * With stream pointing to `app/uploads/profile`, we make sure we can't access `app/uploads/MyFile.txt`.
     */
    public function testFindResourceWithBackPath(): void
    {
        $locator = new ResourceLocator(__DIR__);
        $locator->registerStream('sprinkles', '');
        $locator->registerLocation('uploads', 'app/uploads/profile');

        $result = $locator->findResource('sprinkles://'.'../MyFile.txt');

        $this->assertFalse($result);
    }

    /**
     * Test a location outside of the main path produce the correct relative path.
     */
    public function testFindResourceOutsideMainPath(): void
    {
        $locator = new ResourceLocator(__DIR__.'/Building/Floors');
        $locator->registerStream('files');
        $locator->registerLocation('Garage', __DIR__.'/Building/Garage');

        $resource = $locator->findResource('files://blah.json');

        $this->assertSame(Normalizer::normalizePath(__DIR__).'Building/Garage/files/blah.json', $resource);
    }

    /**
     * Test a location outside of the main path produce the correct relative path.
     */
    public function testListResourceOutsideMainPath(): void
    {
        $locator = new ResourceLocator(__DIR__.'/Building/Floors');
        $locator->registerStream('files');
        $locator->registerLocation('Garage', __DIR__.'/Building/Garage');

        $resources = $locator->listResources('files://', true);

        $this->assertSame(Normalizer::normalizePath(__DIR__).'Building/Garage/files/blah.json', $resources[0]->getAbsolutePath());
    }

    public function testMultipleStreamWithSameScheme(): void
    {
        $locator = new ResourceLocator(__DIR__);
        $locator->registerSharedStream('sprinkles', 'Building/Floors/Floor/');
        $locator->registerSharedStream('sprinkles', 'Building/Floors/Floor3');

        $result = $locator->findResources('sprinkles://files/test.json');

        // We don't care (yet) about the actual result, we just want to make
        // sure the two streams are picked up.
        $this->assertCount(2, $result);
    }
}
