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
use UserFrosting\Exceptions\BakeryClassException;

/**
 * Base class for UserFrosting Bakery CLI tools.
 */
class Bakery extends Cupcake
{
    /**
     * @var Application The Slim application instance.
     */
    protected $app;

    /**
     * {@inheritDoc}
     */
    public function init(): void
    {
        parent::init();

        // Load Bakery commands into Symfony Console Application
        $this->loadCommands();
    }

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
     *
     * @return Application
     */
    protected function createApp(): Application
    {
        $app = new Application('UserFrosting Bakery', \UserFrosting\VERSION);

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
