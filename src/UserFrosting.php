<?php

/*
 * UserFrosting Framework (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/framework
 * @copyright Copyright (c) 2013-2021 Alexander Weissman, Louis Charette, Jordan Mele
 * @license   https://github.com/userfrosting/framework/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting;

use DI\Bridge\Slim\Bridge;
use Psr\Http\Server\MiddlewareInterface;
use Slim\App;
use UserFrosting\Routes\RouteDefinitionInterface;
use UserFrosting\Sprinkle\RecipeExtensionLoader;

/**
 * UserFrosting Main Class.
 */
class UserFrosting extends Cupcake
{
    /**
     * @var App The Slim application instance.
     */
    protected $app;

    /**
     * Initialize the application. Load up Sprinkles and the base app.
     */
    public function init(): void
    {
        parent::init();

        // Load and registering routes
        $this->loadRoutes();

        // Load and register middlewares
        $this->registerMiddlewares();
    }

    /**
     * Return the underlying Slim App instance, if available.
     *
     * @return App
     */
    public function getApp(): App
    {
        return $this->app;
    }

    /**
     * Instantiate the Slim application.
     *
     * @return App
     */
    protected function createApp(): App
    {
        $app = Bridge::create($this->ci);

        return $app;
    }

    /**
     * Run application.
     *
     * @codeCoverageIgnore
     */
    public function run(): void
    {
        $this->app->run();
    }

    /**
     * Load and register all routes.
     */
    protected function loadRoutes(): void
    {
        /** @var RecipeExtensionLoader */
        $extensionLoader = $this->ci->get(RecipeExtensionLoader::class);

        $definitions = $extensionLoader->getInstances(
            method: 'getRoutes',
            extensionInterface: RouteDefinitionInterface::class
        );

        foreach ($definitions as $definition) {
            $definition->register($this->app);
        }
    }

    /**
     * Load and register all middlewares.
     *
     * Note : Middlewares needs to be instanced by CI to bypass Slim Bridge issue
     * when adding MiddlewareInterface. This is done automatically by RecipeExtensionLoader
     * https://github.com/PHP-DI/Slim-Bridge/issues/51
     */
    protected function registerMiddlewares(): void
    {
        /** @var RecipeExtensionLoader */
        $extensionLoader = $this->ci->get(RecipeExtensionLoader::class);

        // Add default Slim middlewares
        $this->app->addBodyParsingMiddleware();
        $this->app->addRoutingMiddleware();

        // Add the registered Middlewares
        $middlewares = $extensionLoader->getInstances(
            method: 'getMiddlewares',
            extensionInterface: MiddlewareInterface::class
        );

        foreach ($middlewares as $middleware) {
            $this->app->add($middleware);
        }
    }
}
