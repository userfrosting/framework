<?php

declare(strict_types=1);

/*
 * UserFrosting Framework (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/framework
 * @copyright Copyright (c) 2013-2021 Alexander Weissman, Louis Charette, Jordan Mele
 * @license   https://github.com/userfrosting/framework/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\ServicesProvider;

use DI\Bridge\Slim\Bridge;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Slim\App as SlimApp;
use Slim\Factory\ServerRequestCreatorFactory;
use Symfony\Component\Console\Application as ConsoleApp;
use Symfony\Component\Console\Command\Command;
use UserFrosting\Event\EventDispatcher;
use UserFrosting\Event\SprinkleListenerProvider;
use UserFrosting\Routes\RouteDefinitionInterface;
use UserFrosting\Sprinkle\RecipeExtensionLoader;

/**
 * Register framework base services.
 */
final class FrameworkService implements ServicesProviderInterface
{
    public function register(): array
    {
        return [
            // Slim App
            SlimApp::class => function (ContainerInterface $ci, RecipeExtensionLoader $extensionLoader) {
                $app = Bridge::create($ci);

                // Register routes & middlewares
                $this->registerRoutes($app, $extensionLoader);
                $this->registerMiddlewares($app, $extensionLoader);

                return $app;
            },

            // Symfony Console Application
            ConsoleApp::class => function (RecipeExtensionLoader $extensionLoader) {
                $version = (string) \Composer\InstalledVersions::getPrettyVersion('userfrosting/framework');
                $app = new ConsoleApp('UserFrosting Bakery', $version);

                // Register commands
                $this->loadCommands($app, $extensionLoader);

                return $app;
            },

            // Request
            ServerRequestInterface::class => function () {
                $serverRequestCreator = ServerRequestCreatorFactory::create();
                $request = $serverRequestCreator->createServerRequestFromGlobals();

                return $request;
            },

            // Events
            EventDispatcherInterface::class  => \DI\autowire(EventDispatcher::class),
            ListenerProviderInterface::class => \DI\autowire(SprinkleListenerProvider::class),
        ];
    }

    /**
     * Load and register all routes.
     *
     * @param SlimApp               $app
     * @param RecipeExtensionLoader $extensionLoader
     */
    protected function registerRoutes(SlimApp $app, RecipeExtensionLoader $extensionLoader): void
    {
        /** @var RouteDefinitionInterface[] */
        $definitions = $extensionLoader->getInstances(
            method: 'getRoutes',
            extensionInterface: RouteDefinitionInterface::class
        );

        foreach ($definitions as $definition) {
            $definition->register($app);
        }
    }

    /**
     * Load and register all middlewares.
     *
     * @param SlimApp               $app
     * @param RecipeExtensionLoader $extensionLoader
     */
    protected function registerMiddlewares(SlimApp $app, RecipeExtensionLoader $extensionLoader): void
    {
        // Add default Slim middlewares
        $app->addBodyParsingMiddleware();
        $app->addRoutingMiddleware();

        // Add the registered Middlewares
        /** @var MiddlewareInterface[] */
        $middlewares = $extensionLoader->getObjects(
            method: 'getMiddlewares',
            extensionInterface: MiddlewareInterface::class
        );

        foreach ($middlewares as $middleware) {
            $app->add($middleware);
        }
    }

    /**
     * Load and register all defined bakery commands.
     *
     * @param ConsoleApp            $app
     * @param RecipeExtensionLoader $extensionLoader
     */
    protected function loadCommands(ConsoleApp $app, RecipeExtensionLoader $extensionLoader): void
    {
        /** @var Command[] */
        $commands = $extensionLoader->getInstances(
            method: 'getBakeryCommands',
            extensionInterface: Command::class
        );

        foreach ($commands as $command) {
            $app->add($command);
        }
    }
}
