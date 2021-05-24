<?php

/*
 * UserFrosting Framework (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/framework
 * @copyright Copyright (c) 2013-2021 Alexander Weissman, Louis Charette, Jordan Mele
 * @license   https://github.com/userfrosting/framework/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting;

use DI\Container;
use DI\ContainerBuilder;
use RocketTheme\Toolbox\Event\Event;
use RocketTheme\Toolbox\Event\EventDispatcher;
use Slim\App;
use UserFrosting\Sprinkle\SprinkleManager;

/**
 * Base class for UserFrosting application.
 */
abstract class Cupcake
{
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
     * @param string $mainSprinkle
     */
    public function __construct(protected string $mainSprinkle)
    {
        $this->init();
    }

    /**
     * Initialize the application. Load up Sprinkles and the base app.
     */
    public function init(): void
    {
        // Setup sprinkles
        $this->setupSprinkles();

        // First, we create our DI container
        $this->ci = $this->createContainer();

        // Set up facade reference to container.
        Facade::setFacadeContainer($this->ci);

        // Note that the application is required for the SprinkleManager to set up routes.
        $this->app = $this->createApp();

        // Register SprinkleManager into the CI
        $this->ci->set('sprinkleManager', $this->sprinkleManager);

        // $slimAppEvent = new SlimAppEvent($this->app);

        // $this->fireEvent('onAppInitialize', $slimAppEvent);

        // Add global middleware
        // $this->fireEvent('onAddGlobalMiddleware', $slimAppEvent);

        // Register the App itself into the CI
        $this->ci->set(App::class, $this->app);
    }

    /**
     * Run application.
     */
    abstract public function run(): void;

    /**
     * Return the underlying Slim or Symfony App instance, if available.
     *
     * @return mixed
     */
    abstract public function getApp();

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
     * Get constructor.
     */
    public function getMainSprinkle(): string
    {
        return $this->mainSprinkle;
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
     * Create the container with all sprinkles services definitions.
     *
     * @return Container
     */
    protected function createContainer(): Container
    {
        $builder = new ContainerBuilder();
        $builder->addDefinitions($this->sprinkleManager->getServicesDefinitions());
        $ci = $builder->build();

        return $ci;
    }

    /**
     * Instantiate the Slim or Symfony application.
     *
     * @return mixed
     */
    abstract protected function createApp();

    /**
     * Register system services, load all sprinkles, and add their resources and services.
     */
    protected function setupFrameworkServices(): void
    {
        // Register system services
        // $serviceProvider = new ServicesProvider();
        // $serviceProvider->register($this->ci);
    }

    /**
     * Register system services, load all sprinkles, and add their resources and services.
     * Boot the Sprinkle manager, which creates Sprinkle classes and subscribes them to the event dispatcher
     */
    protected function setupSprinkles(): void
    {
        $this->sprinkleManager = new SprinkleManager($this->mainSprinkle);

        // $this->fireEvent('onSprinklesInitialized');

        // Add Sprinkle resources (assets, templates, etc) to locator
        // $sprinkleManager->addResources();
        // $this->fireEvent('onSprinklesAddResources');

        // Register Sprinkle services
        // $sprinkleManager->registerAllServices();
        // $this->fireEvent('onSprinklesRegisterServices');
    }
}
