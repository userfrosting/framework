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
     *
     * @return App
     */
    protected function initiateApp(): App
    {
        return $this->ci->get(App::class);
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
