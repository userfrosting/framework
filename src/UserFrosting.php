<?php

/*
 * UserFrosting Framework (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/framework
 * @copyright Copyright (c) 2013-2021 Alexander Weissman, Louis Charette, Jordan Mele
 * @license   https://github.com/userfrosting/framework/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting;

use RocketTheme\Toolbox\Event\Event;
use RocketTheme\Toolbox\Event\EventDispatcher;
use Slim\App;
use Slim\Container;
use Symfony\Component\Console\Application;
use UserFrosting\Sprinkle\SprinkleManager;
use UserFrosting\Sprinkle\SprinkleReceipe;

/**
 * UserFrosting Main Class.
 */
class UserFrosting
{
    /**
     * @var App|Application The Slim or Symfony application instance.
     */
    protected $app;

    /**
     * @var Container The global container object, which holds all your services.
     */
    protected $ci;

    /**
     * @var SprinkleManager
     */
    protected $sprinkleManager;

    /**
     * Constructor.
     *
     * @param SprinkleReceipe $ngredients
     */
    public function __construct(
        protected SprinkleReceipe $mainSprinkle
    ) {
    }

    //TODO : set/get default sprinkle

    /**
     * Initialize the application. Load up Sprinkles and the base app.
     *
     * @return static
     */
    public function init(): static
    {
        // First, we create our DI container
        $this->ci = $this->createContainer();

        // Set up facade reference to container.
        Facade::setFacadeContainer($this->ci);

        // Setup sprinkles
        $this->setupSprinkles();

        // Set the configuration settings for Slim in the 'settings' service
        // $this->ci->settings = $this->ci->config['settings'];

        // Note that the application is required for the SprinkleManager to set up routes.
        $this->app = $this->createApp();

        // $slimAppEvent = new SlimAppEvent($this->app);

        // $this->fireEvent('onAppInitialize', $slimAppEvent);

        // Add global middleware
        // $this->fireEvent('onAddGlobalMiddleware', $slimAppEvent);

        return $this;
    }

    /**
     * Run application.
     */
    public function run(): void
    {
        $this->app->run();
    }

    /**
     * Return the underlying Slim App instance, if available.
     *
     * @return App|Application
     */
    public function getApp(): App | Application
    {
        return $this->app;
    }

    /**
     * Return the DI container.
     *
     * @return Container
     */
    public function getContainer(): Container
    {
        return $this->ci;
    }

    /**
     * Fires an event with optional parameters.
     *
     * @param string     $eventName
     * @param Event|null $event
     *
     * @return Event
     */
    // public function fireEvent($eventName, Event $event = null)
    // {
    //     /** @var EventDispatcher */
    //     $eventDispatcher = $this->ci->eventDispatcher;

    //     return $eventDispatcher->dispatch($eventName, $event);
    // }

    /**
     * Create the container.
     *
     * @return Container
     */
    protected function createContainer(): Container
    {
        $ci = new Container();

        return $ci;
    }

    /**
     * Instantiate the Slim application.
     *
     * @return App|Application
     */
    protected function createApp(): App | Application
    {
        $app = new App($this->ci);

        return $app;
    }

    /**
     * Register system services, load all sprinkles, and add their resources and services.
     */
    protected function setupSprinkles(): void
    {
        // Register system services
        // $serviceProvider = new ServicesProvider();
        // $serviceProvider->register($this->ci);

        // Boot the Sprinkle manager, which creates Sprinkle classes and subscribes them to the event dispatcher
        /** @var \UserFrosting\System\Sprinkle\SprinkleManager */
        // $sprinkleManager = $this->ci->sprinkleManager;
        // TODO : Move to services
        $this->sprinkleManager = new SprinkleManager($this->mainSprinkle);
        $this->sprinkleManager->loadSprinkles();

        // $this->fireEvent('onSprinklesInitialized');

        // Add Sprinkle resources (assets, templates, etc) to locator
        // $sprinkleManager->addResources();
        // $this->fireEvent('onSprinklesAddResources');

        // Register Sprinkle services
        // $sprinkleManager->registerAllServices();
        // $this->fireEvent('onSprinklesRegisterServices');
    }
}
