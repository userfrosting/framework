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

            // TODO : Test a closure that don't accept "App"

            // Run Closure
            $definition($this->app);
        }
    }
}
