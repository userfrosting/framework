<?php

/*
 * UserFrosting Framework (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/framework
 * @copyright Copyright (c) 2013-2021 Alexander Weissman, Louis Charette, Jordan Mele
 * @license   https://github.com/userfrosting/framework/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting;

use Slim\App;
use UserFrosting\Event\AppInitiatedEvent;
use UserFrosting\Event\EventDispatcher;

/**
 * UserFrosting Main Class.
 */
final class UserFrosting extends Cupcake
{
    /**
     * @var App The Slim application instance.
     */
    protected App $app;

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
     */
    protected function initiateApp(): void
    {
        $this->app = $this->ci->get(App::class);

        // Dispatch AppInitiatedEvent
        $eventDispatcher = $this->ci->get(EventDispatcher::class);
        $eventDispatcher->dispatch(new AppInitiatedEvent());
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
}
