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
use PHPUnit\Framework\TestCase as BaseTestCase;
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
    use CustomAssertionsTrait;

    /**
     * The global container object, which holds all services.
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
     * Refresh the application instance by setting up a basic UF app.
     *
     * @codeCoverageIgnore
     */
    protected function refreshApplication(): void
    {
        $this->userfrosting = new UserFrosting($this->mainSprinkle);
        $this->app = $this->userfrosting->getApp();
        $this->ci = $this->userfrosting->getContainer();
    }

    /**
     * Create a server request.
     *
     * @param string              $method       The HTTP method
     * @param string|UriInterface $uri          The URI
     * @param mixed[]             $serverParams The server parameters
     *
     * @return ServerRequestInterface
     * @codeCoverageIgnore
     */
    protected function createRequest(
        string $method,
        string|UriInterface $uri,
        array $serverParams = []
    ): ServerRequestInterface {
        $request = new ServerRequestFactory();

        return $request->createServerRequest($method, $uri, $serverParams);
    }

    /**
     * Create a JSON request.
     *
     * @param string              $method The HTTP method
     * @param string|UriInterface $uri    The URI
     * @param mixed[]|null        $data   The json data
     *
     * @return ServerRequestInterface
     * @codeCoverageIgnore
     */
    protected function createJsonRequest(
        string $method,
        string|UriInterface $uri,
        ?array $data = null
    ): ServerRequestInterface {
        $request = $this->createRequest($method, $uri);

        if ($data !== null) {
            $request = $request->withParsedBody($data);
        }

        return $request->withHeader('Accept', 'application/json');
    }

    /**
     * Handle request and returns the response.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     * @codeCoverageIgnore
     */
    protected function handleRequest(ServerRequestInterface $request): ResponseInterface
    {
        return $this->app->handle($request);
    }
}
