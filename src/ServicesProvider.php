<?php

/*
 * UserFrosting Framework (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/framework
 * @copyright Copyright (c) 2013-2021 Alexander Weissman, Louis Charette, Jordan Mele
 * @license   https://github.com/userfrosting/framework/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\System;

use Psr\Container\ContainerInterface;
use RocketTheme\Toolbox\Event\EventDispatcher;

/**
 * UserFrosting system services provider.
 *
 * Registers system services for UserFrosting, such as file locator, event dispatcher, and sprinkle manager.
 *
 * @author Alex Weissman (https://alexanderweissman.com)
 */
class ServicesProvider
{
    /**
     * Register UserFrosting's system services.
     *
     * @param ContainerInterface $container A DI container implementing ArrayAccess and psr-container.
     */
    public function register(ContainerInterface $container)
    {
        /*
         * Set up the event dispatcher, required by Sprinkles to hook into the UF lifecycle.
         *
         * @return \RocketTheme\Toolbox\Event\EventDispatcher
         */
        $container['eventDispatcher'] = function ($c) {
            return new EventDispatcher();
        };
    }
}
