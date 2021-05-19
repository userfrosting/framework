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
use RocketTheme\Toolbox\Event\Event;
use RocketTheme\Toolbox\Event\EventDispatcher;
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
    public function __construct(
        protected string $mainSprinkle
    ) {
        $this->init();
    }

    //TODO : set/get default sprinkle

    /**
     * Initialize the application. Load up Sprinkles and the base app.
     */
    public function init(): void
    {
        // Setup sprinkles
        $this->setupSprinkles();

        // TODO : REGISTER SERVICES DEFINITIONS
        
        // First, we create our DI container
        $this->ci = $this->createContainer();

        // Set up facade reference to container.
        Facade::setFacadeContainer($this->ci);

        // Set the configuration settings for Slim in the 'settings' service
        // $this->ci->settings = $this->ci->config['settings'];

        // Note that the application is required for the SprinkleManager to set up routes.
        $this->app = $this->createApp();

        // TODO : REGISTER SERVICES USING SET
        $this->sprinkleManager->registerServices($this->ci);

        // TODO :: Register SprinkleManager into the CI
        // $this->ci->set('sprinkleManager', \DI\create(MessageGenerator::class));

        // $slimAppEvent = new SlimAppEvent($this->app);

        // $this->fireEvent('onAppInitialize', $slimAppEvent);

        // Add global middleware
        // $this->fireEvent('onAddGlobalMiddleware', $slimAppEvent);
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
        // $builder = new \DI\ContainerBuilder();
        // $builder->addDefinitions([
        //     // place your definitions here
        //     'messageGenerator' => \DI\create(MessageGenerator::class)
        // ]);
        // $ci = $builder->build();

        $ci = new Container();

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

        // $this->fireEvent('onSprinklesInitialized');

        // Add Sprinkle resources (assets, templates, etc) to locator
        // $sprinkleManager->addResources();
        // $this->fireEvent('onSprinklesAddResources');

        // Register Sprinkle services
        // $sprinkleManager->registerAllServices();
        // $this->fireEvent('onSprinklesRegisterServices');
    }
}
