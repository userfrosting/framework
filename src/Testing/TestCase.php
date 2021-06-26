<?php

/*
 * UserFrosting Framework (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/framework
 * @copyright Copyright (c) 2013-2021 Alexander Weissman, Louis Charette, Jordan Mele
 * @license   https://github.com/userfrosting/framework/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Testing;

use DI\Container;
use JsonException;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Slim\App;
use Slim\Psr7\Factory\ServerRequestFactory;
use UserFrosting\UserFrosting;

/**
 * Case for test that requires the full App instance.
 * This can be used for HTTP testing against the real deal.
 */
class TestCase extends BaseTestCase
{
    /**
     * The global container object, which holds all services.
     *
     * @var Container
     */
    protected Container $ci;

    /**
     * The Slim App Instance.
     */
    protected App $app;

    /**
     * The UF app instance.
     */
    protected UserFrosting $userfrosting;

    /**
     * String reference to SprinkleRecipe.
     */
    protected string $mainSprinkle;

    /**
     * Setup the test environment.
     *
     * @codeCoverageIgnore
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->refreshApplication();
    }

    /**
     * Refresh the application instance.
     *
     * @codeCoverageIgnore
     */
    protected function refreshApplication(): void
    {
        // Force setting UF_MODE.  This is needed at the moment because Bakery
        // uses passthru to invoke PHPUnit.  This means that the value of UF_MODE
        // has already been set when Bakery was set up, and PHPUnit < 6.3 has
        // no way to override environment vars that have already been set.
        putenv('UF_MODE=testing');

        // Setup the base UF app
        $this->userfrosting = new UserFrosting($this->mainSprinkle);
        $this->app = $this->userfrosting->getApp();
        $this->ci = $this->userfrosting->getContainer();
    }

    /**
     * Create a server request.
     *
     * @param string              $method       The HTTP method
     * @param string|UriInterface $uri          The URI
     * @param array               $serverParams The server parameters
     *
     * @return ServerRequestInterface
     * @codeCoverageIgnore
     */
    protected function createRequest(string $method, $uri, array $serverParams = []): ServerRequestInterface
    {
        $request = new ServerRequestFactory();

        return $request->createServerRequest($method, $uri, $serverParams);
    }

    /**
     * Create a JSON request.
     *
     * @param string              $method The HTTP method
     * @param string|UriInterface $uri    The URI
     * @param array|null          $data   The json data
     *
     * @return ServerRequestInterface
     * @codeCoverageIgnore
     */
    protected function createJsonRequest(string $method, $uri, array $data = null): ServerRequestInterface
    {
        $request = $this->createRequest($method, $uri);

        if ($data !== null) {
            $request = $request->withParsedBody($data);
        }

        return $request->withHeader('Content-Type', 'application/json');
    }

    /**
     * Handle request and returns the response.
     *
     * @param RequestInterface $request
     *
     * @return ResponseInterface
     * @codeCoverageIgnore
     */
    protected function handleRequest(RequestInterface $request): ResponseInterface
    {
        return $this->app->handle($request);
    }

    /**
     * Verify that the given string is an exact match for the body returned.
     *
     * @param string            $expected The expected string
     * @param ResponseInterface $response The response
     * @codeCoverageIgnore
     */
    protected function assertResponse(string $expected, ResponseInterface $response): void
    {
        $this->assertSame($expected, (string) $response->getBody());
    }

    /**
     * Verify that the given array is an exact match for the JSON returned.
     *
     * @param array             $expected The expected array
     * @param ResponseInterface $response The response
     *
     * @throws JsonException
     * @codeCoverageIgnore
     */
    protected function assertResponseJson(array $expected, ResponseInterface $response): void
    {
        $actual = (string) $response->getBody();
        $this->assertSame($expected, (array) json_decode($actual, true, 512, JSON_THROW_ON_ERROR));
    }
}
