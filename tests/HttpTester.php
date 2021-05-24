<?php

/*
 * UserFrosting Framework (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/framework
 * @copyright Copyright (c) 2013-2021 Alexander Weissman, Louis Charette, Jordan Mele
 * @license   https://github.com/userfrosting/framework/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Tests;

use DI\Container;
use InvalidArgumentException;
use JsonException;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Slim\App;
use Slim\Psr7\Factory\ServerRequestFactory;
use UnexpectedValueException;

/**
 * HttpTester Trait.
 *
 * @see https://odan.github.io/2020/06/09/slim4-testing.html#http-tests
 */
trait HttpTester
{
    /**
     * @var Container
     */
    // protected $container;

    /**
     * @var App
     */
    // protected $app;

    /**
     * Bootstrap app.
     *
     * @throws UnexpectedValueException
     */
    /*protected function setUp(): void
    {
        $this->app = require __DIR__ . '/../../config/bootstrap.php';

        $container = $this->app->getContainer();
        if ($container === null) {
            throw new UnexpectedValueException('Container must be initialized');
        }

        $this->container = $container;
    }*/

    /**
     * Add mock to container.
     *
     * @param string $class The class or interface
     *
     * @return MockObject The mock
     */
    /*protected function mock(string $class): MockObject
    {
        if (!class_exists($class)) {
            throw new InvalidArgumentException(sprintf('Class not found: %s', $class));
        }

        $mock = $this->getMockBuilder($class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->container->set($class, $mock);

        return $mock;
    }*/

    /**
     * Create a server request.
     *
     * @param string              $method       The HTTP method
     * @param string|UriInterface $uri          The URI
     * @param array               $serverParams The server parameters
     *
     * @return ServerRequestInterface
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
     * Verify that the given string is an exact match for the body returned.
     *
     * @param string            $expected The expected string
     * @param ResponseInterface $response The response
     */
    protected function assertResponseData(string $expected, ResponseInterface $response): void
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
     */
    protected function assertResponseJson(array $expected, ResponseInterface $response): void
    {
        $actual = (string) $response->getBody();
        $this->assertSame($expected, (array) json_decode($actual, true, 512, JSON_THROW_ON_ERROR));
    }
}
