<?php

/*
 * UserFrosting Framework (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/framework
 * @copyright Copyright (c) 2013-2021 Alexander Weissman, Louis Charette, Jordan Mele
 * @license   https://github.com/userfrosting/framework/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Tests\Assets\ServeAsset;

use PHPUnit\Framework\TestCase;
use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Response;
use UserFrosting\Assets\AssetLoader;
use UserFrosting\Assets\Assets;
use UserFrosting\Assets\ServeAsset\SlimServeAsset;
use UserFrosting\UniformResourceLocator\ResourceLocator;

/**
 * Tests SlimServeAsset class.
 */
class SlimServeAssetTest extends TestCase
{
    /** @var AssetLoader */
    private $assetLoader;

    /**
     * Initializes test environment.
     */
    public function setUp(): void
    {
        $basePath = __DIR__.'/../data';
        $baseUrl = 'https://assets.userfrosting.com/assets/';
        $locatorScheme = 'assets';
        $locator = new ResourceLocator($basePath);
        $locator->registerStream($locatorScheme, '', 'assets');
        $locator->registerStream($locatorScheme, 'vendor', 'assets', true);
        $locator->registerLocation('hawks', 'sprinkles/hawks/');
        $locator->registerLocation('owls', 'sprinkles/owls/');

        // Initialize Assets
        $assets = new Assets($locator, $locatorScheme, $baseUrl);

        // Initialize container
        $this->assetLoader = new AssetLoader($assets);
    }

    /**
     * Tests SlimServeAsset constructor.
     *
     * @return SlimServeAsset
     */
    public function testConstructor()
    {
        $server = new SlimServeAsset($this->assetLoader);
        $this->assertInstanceOf(SlimServeAsset::class, $server);

        return $server;
    }

    /**
     * Test with non-existent asset.
     *
     * @param SlimServeAsset $controller
     *
     *
     * @depends testConstructor
     */
    public function testInaccessibleAsset(SlimServeAsset $controller)
    {
        // Create environment.
        $environment = Environment::mock([]);

        // Create request and response objects.
        $request = Request::createFromEnvironment($environment);
        $response = new Response();

        // Invoke controller method.
        $response = $controller->serveAsset($request, $response, [
            'url' => 'forbidden.txt',
        ]);

        // Assert 404 response
        $this->assertSame($response->getStatusCode(), 404);

        // Assert empty response body
        $this->assertSame($response->getBody()->getContents(), '');
    }

    /**
     * Test with existent asset.
     *
     * @param SlimServeAsset $controller
     *
     *
     * @depends testConstructor
     */
    public function testAssetMatchesExpectations(SlimServeAsset $controller)
    {
        // Create environment.
        $environment = Environment::mock([]);

        // Create request and response objects.
        $request = Request::createFromEnvironment($environment);
        $response = new Response();

        // Invoke controller method.
        $response = $controller->serveAsset($request, $response, [
            'url' => 'allowed.txt',
        ]);

        // Assert 200 response
        $this->assertSame($response->getStatusCode(), 200);

        // Assert response body matches file
        $this->assertSame($response->getBody()->__toString(), file_get_contents(__DIR__.'/../data/sprinkles/hawks/assets/allowed.txt'));

        // Assert correct MIME
        $this->assertSame($response->getHeader('Content-Type'), ['text/plain']);
    }

    /**
     * Test with existent asset.
     *
     * @param SlimServeAsset $controller
     *
     *
     * @depends testConstructor
     */
    public function testAssetOfUnknownType(SlimServeAsset $controller)
    {
        // Create environment.
        $environment = Environment::mock([]);

        // Create request and response objects.
        $request = Request::createFromEnvironment($environment);
        $response = new Response();

        // Invoke controller method.
        $response = $controller->serveAsset($request, $response, [
            'url' => 'mysterious',
        ]);

        // Assert 200 response
        $this->assertSame($response->getStatusCode(), 200);

        // Assert response body matches file
        $this->assertSame($response->getBody()->__toString(), file_get_contents(__DIR__.'/../data/sprinkles/hawks/assets/mysterious'));

        // Assert correct MIME
        $this->assertSame($response->getHeader('Content-Type'), ['text/plain']);
    }
}
