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
use Slim\App;

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
        foreach ($this->sprinkleManager->getRoutesDefinitions() as $definition) {
            $definition->register($this->app);
        }
    }

    /**
     * Load and register all middlewares.
     */
    protected function registerMiddlewares(): void
    {
        // Add default Slim middlewares
        $this->app->addBodyParsingMiddleware();
        $this->app->addRoutingMiddleware();

        // Add Sprinkles Middlewares
        foreach ($this->sprinkleManager->getMiddlewaresDefinitions() as $middleware) {
            // Bypass Slim Bridge issue when adding MiddlewareInterface
            // @see https://github.com/PHP-DI/Slim-Bridge/issues/51
            // $this->app->add($middleware);
            $this->app->add($this->ci->get($middleware));
        }
    }
}
