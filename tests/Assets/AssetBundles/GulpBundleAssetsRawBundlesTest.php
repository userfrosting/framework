<?php

/*
 * UserFrosting Framework (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/framework
 * @copyright Copyright (c) 2013-2021 Alexander Weissman, Louis Charette, Jordan Mele
 * @license   https://github.com/userfrosting/framework/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Tests\Assets\AssetBundles;

use PHPUnit\Framework\TestCase;
use UserFrosting\Assets\AssetBundles\GulpBundleAssetsRawBundles;
use UserFrosting\Assets\Assets;
use UserFrosting\Assets\Exception\InvalidBundlesFileException;
use UserFrosting\Support\Exception\FileNotFoundException;
use UserFrosting\Support\Exception\JsonException;

/**
 * Tests GulpBundleAssetsRawBundles class.
 */
class GulpBundleAssetsRawBundlesTest extends TestCase
{
    /**
     * Tests GulpBundleAssetsRawBundles constructor.
     * Returns the created GulpBundleAssetsRawBundles instance for use by dependent tests.
     *
     * @return Assets
     */
    public function testConstruct()
    {
        $bundles = new GulpBundleAssetsRawBundles(__DIR__.'/../data/bundle.config.json');
        $this->assertInstanceOf(GulpBundleAssetsRawBundles::class, $bundles);

        return $bundles;
    }

    /**
     * Tests GulpBundleAssetsRawBundles constructor with a config that has no bundles property.
     */
    public function testConstructStubConfig()
    {
        $bundles = new GulpBundleAssetsRawBundles(__DIR__.'/../data/bundle.config.stub.json');
        $this->assertInstanceOf(GulpBundleAssetsRawBundles::class, $bundles);
    }

    /**
     * Tests GulpBundleAssetsRawBundles constructor with config containing invalid syntax.
     */
    public function testConstructInvalidSyntax()
    {
        $this->expectException(JsonException::class);
        new GulpBundleAssetsRawBundles(__DIR__.'/../data/bundle.config.invalid-syntax.json');
    }

    /**
     * Tests GulpBundleAssetsRawBundles constructor with missing config.
     */
    public function testConstructNotFound()
    {
        $this->expectException(FileNotFoundException::class);
        new GulpBundleAssetsRawBundles(__DIR__.'/../data/bundle.config.not-here.json');
    }

    /**
     * Tests GulpBundleAssetsRawBundles constructor when the bundle property is the incorrect type.
     */
    public function testConstructInvalidBundlesPropertyType()
    {
        $this->expectException(InvalidBundlesFileException::class);
        new GulpBundleAssetsRawBundles(__DIR__.'/../data/bundle.config.bad-bundle.json');
    }

    /**
     * Tests GulpBundleAssetsRawBundles constructor when a bundle contains an invalid styles property.
     */
    public function testConstructInvalidStylesBundle()
    {
        $this->expectException(InvalidBundlesFileException::class);
        new GulpBundleAssetsRawBundles(__DIR__.'/../data/bundle.config.bad-styles.json');
    }

    /**
     * Tests GulpBundleAssetsRawBundles constructor when a bundle contains an invalid scripts property.
     */
    public function testConstructInvalidJsBundle()
    {
        $this->expectException(InvalidBundlesFileException::class);
        new GulpBundleAssetsRawBundles(__DIR__.'/../data/bundle.config.bad-scripts.json');
    }

    /**
     * Tests getCssBundleAssets method.
     *
     * @param GulpBundleAssetsRawBundles $bundles
     *
     *
     * @depends testConstruct
     */
    public function testGetCssBundleAssets(GulpBundleAssetsRawBundles $bundles)
    {
        $this->assertEquals($bundles->getCssBundleAssets('test'), [
            'bootstrap/css/bootstrap.css',
        ]);
    }

    /**
     * Tests that getCssBundleAssets method throws an exception when requested bundle doesn't exist.
     *
     * @param GulpBundleAssetsRawBundles $bundles
     *
     *
     * @depends testConstruct
     */
    public function testGetCssBundleAssetsOutOfRange(GulpBundleAssetsRawBundles $bundles)
    {
        $this->expectException(\OutOfRangeException::class);
        $bundles->getCssBundleAssets('owls');
    }

    /**
     * Tests getJsBundleAssets method.
     *
     * @param GulpBundleAssetsRawBundles $bundles
     *
     *
     * @depends testConstruct
     */
    public function testGetJsBundleAssets(GulpBundleAssetsRawBundles $bundles)
    {
        $this->assertEquals($bundles->getJsBundleAssets('test'), [
            'bootstrap/js/bootstrap.js',
            'bootstrap/js/npm.js',
        ]);
    }

    /**
     * Tests that getJsBundleAssets method throws an exception when requested bundle doesn't exist.
     *
     * @param GulpBundleAssetsRawBundles $bundles
     *
     *
     * @depends testConstruct
     */
    public function testGetJsBundleAssetsOutOfRange(GulpBundleAssetsRawBundles $bundles)
    {
        $this->expectException(\OutOfRangeException::class);
        $bundles->getJsBundleAssets('owls');
    }

    /**
     * Tests that an `InvalidBundlesFileException` is trown if bundle is not an array of string.
     * Note that the exception won't be `InvalidArgumentException`, as that exception in `standardiseBundle`
     * is catched in `__construct` and retrown.
     */
    public function testNonStringAssets()
    {
        $this->expectException(InvalidBundlesFileException::class);
        $bundles = new GulpBundleAssetsRawBundles(__DIR__.'/../data/bundle.config.not-array.json');
    }
}
