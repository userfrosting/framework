<?php

/*
 * UserFrosting Framework (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/framework
 * @copyright Copyright (c) 2013-2021 Alexander Weissman, Louis Charette, Jordan Mele
 * @license   https://github.com/userfrosting/framework/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Bakery;

use Slim\App;
use Symfony\Component\Console\Application;
use UserFrosting\Exceptions\BakeryClassException;
use UserFrosting\UserFrosting;

/**
 * Base class for UserFrosting Bakery CLI tools.
 */
class Bakery extends UserFrosting
{
    /**
     * {@inheritDoc}
     */
    public function init(): static
    {
        parent::init();

        // Load Bakery commands into Symfony Console Application
        $this->loadCommands();

        return $this;
    }

    /**
     * Create Symfony Console App.
     *
     * @return App|Application
     */
    protected function createApp(): App | Application
    {
        $app = new Application('UserFrosting Bakery', \UserFrosting\VERSION);

        return $app;
    }

    /**
     * Return the list of available commands for a specific sprinkle.
     */
    protected function loadCommands(): void
    {
        foreach ($this->sprinkleManager->getBakeryCommands() as $command) {
            // Get command instance
            $instance = new $command();

            // Class must be an instance of symfony command
            if (!$instance instanceof CommandReceipe) {
                throw new BakeryClassException('Bakery command `'.$instance::class.'` must be instance of ' . CommandReceipe::class);
            }

            // Add command to the Console app
            $instance->setContainer($this->ci);
            $this->app->add($instance);
        }
    }
}
