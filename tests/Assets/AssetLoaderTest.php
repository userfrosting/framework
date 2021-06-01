<?php

/*
 * UserFrosting Framework (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/framework
 * @copyright Copyright (c) 2013-2021 Alexander Weissman, Louis Charette, Jordan Mele
 * @license   https://github.com/userfrosting/framework/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Tests\Assets;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use UserFrosting\Assets\AssetLoader;
use UserFrosting\Assets\Assets;

/**
 * Tests AssetLoader class.
 */
class AssetLoaderTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * Test with non-existent asset.
     */
    public function testInaccessibleAsset()
    {
        $assets = Mockery::mock(Assets::class);
        $assets
            ->shouldReceive('urlPathToAbsolutePath')
            ->with('forbidden.txt')
            ->andReturn('forbidden.txt');
        
        $loader = new AssetLoader($assets);
        $this->assertInstanceOf(AssetLoader::class, $loader);
        $result = $loader->loadAsset('forbidden.txt');

        // Assertions
        $this->assertFalse($result);
    }

    /**
     * Test with existent asset.
     */
    public function testAssetMatchesExpectations()
    {
        $assets = Mockery::mock(Assets::class);
        $assets
            ->shouldReceive('urlPathToAbsolutePath')
            ->with('allowed.txt')
            ->andReturn(__DIR__ . '/data/sprinkles/hawks/assets/allowed.txt');
        
        $loader = new AssetLoader($assets);

        // Assertions
        $this->assertTrue($loader->loadAsset('allowed.txt'));
        $this->assertSame(file_get_contents(__DIR__.'/data/sprinkles/hawks/assets/allowed.txt'), $loader->getContent());
        $this->assertSame(8, $loader->getLength());
        $this->assertSame('text/plain', $loader->getType());
    }

    /**
     * Test with existent asset.
     */
    public function testAssetOfUnknownType()
    {
        $assets = Mockery::mock(Assets::class);
        $assets
            ->shouldReceive('urlPathToAbsolutePath')
            ->with('mysterious')
            ->andReturn(__DIR__ . '/data/sprinkles/hawks/assets/mysterious');
        
        $loader = new AssetLoader($assets);
        $result = $loader->loadAsset('mysterious');

        // Assertions
        $this->assertTrue($result);
        $this->assertSame('text/plain', $loader->getType());
    }
}
