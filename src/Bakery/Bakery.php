<?php

/*
 * UserFrosting Framework (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/framework
 * @copyright Copyright (c) 2013-2021 Alexander Weissman, Louis Charette, Jordan Mele
 * @license   https://github.com/userfrosting/framework/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Bakery;

use Symfony\Component\Console\Application;
use UserFrosting\Cupcake;
use UserFrosting\Event\BakeryInitiatedEvent;
use UserFrosting\Event\EventDispatcher;

/**
 * Base class for UserFrosting Bakery CLI tools.
 */
final class Bakery extends Cupcake
{
    /**
     * @var Application The Slim application instance.
     */
    protected Application $app;

    /**
     * Return the underlying Slim App instance, if available.
     *
     * @return Application
     */
    public function getApp(): Application
    {
        return $this->app;
    }

    /**
     * Create Symfony Console App.
     */
    protected function initiateApp(): void
    {
        $this->app = $this->ci->get(Application::class);

        // Dispatch AppInitiatedEvent
        $eventDispatcher = $this->ci->get(EventDispatcher::class);
        $eventDispatcher->dispatch(new BakeryInitiatedEvent());
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
