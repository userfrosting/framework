<?php

/*
 * UserFrosting Framework (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/framework
 * @copyright Copyright (c) 2013-2021 Alexander Weissman, Louis Charette, Jordan Mele
 * @license   https://github.com/userfrosting/framework/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Sprinkle;

use Psr\Http\Server\MiddlewareInterface;
use ReflectionClass;
use Symfony\Component\Console\Command\Command;
use UserFrosting\Exceptions\BadInstanceOfException;
use UserFrosting\Exceptions\BakeryClassException;
use UserFrosting\Exceptions\SprinkleClassException;
use UserFrosting\Routes\RouteDefinitionInterface;
use UserFrosting\ServicesProvider\ServicesProviderInterface;
use UserFrosting\Support\Exception\NotFoundException;

/**
 * Sprinkle Manager.
 *
 * Manages a collection of loaded Sprinkles for the application.
 * Returns all the informations about the loaded Sprinkles, defined in each SrpinkleReceipe.
 * This class does not perform any action on the application itself, it only serves informations.
 * All routes, commands or other registration process is handled elsewhere.
 *
 * @property string $mainSprinkle
 */
class SprinkleManager
{
    /**
     * @var SprinkleReceipe[] List of avaialable sprinkles
     */
    protected $sprinkles = [];

    /**
     * @var SprinkleReceipe Main sprinkle
     */
    protected $mainSprinkle;

    /**
     * Load sprinkles on construction.
     *
     * @param SprinkleReceipe $mainSprinkle
     */
    public function __construct($mainSprinkle)
    {
        $this->mainSprinkle = $mainSprinkle;
        $this->loadSprinkles();
    }

    /**
     * Generate the list of all loaded sprinkles throught the main sprinkle dependencies.
     */
    public function loadSprinkles(): void
    {
        $this->sprinkles = $this->getDependentSprinkles($this->mainSprinkle);
    }

    /**
     * Return a list for the registered bakery commands, recursilvey.
     *
     * @return Command[]
     */
    public function getBakeryCommands(): array
    {
        $commands = [];

        foreach ($this->sprinkles as $sprinkle) {
            $sprinkleCommands = array_map([$this, 'validateCommand'], $sprinkle::getBakeryCommands());
            $commands = array_merge($commands, $sprinkleCommands);
        }

        return $commands;
    }

    /**
     * Returns a list of all routes definition files from all sprinkles.
     *
     * @return RouteDefinitionInterface[] List of PHP files containing routes definitions.
     */
    public function getRoutesDefinitions(): array
    {
        $routes = [];

        foreach ($this->sprinkles as $sprinkle) {
            foreach ($sprinkle::getRoutes() as $route) {
                $routes[] = $this->validateRouteClass($route);
            }
        }

        return $routes;
    }

    /**
     * Returns a list of all PHP-DI services/container definition files, from all sprinkles.
     *
     * @return ServicesProviderInterface[] List of PHP files containing routes definitions.
     */
    public function getServicesDefinitions(): array
    {
        $containers = [];

        foreach ($this->sprinkles as $sprinkle) {
            foreach ($sprinkle::getServices() as $container) {
                $containers = array_merge($this->validateServicesProvider($container)->register(), $containers);
            }
        }

        return $containers;
    }

    /**
     * Returns a list of all Middlewares, from all sprinkles.
     *
     * @return MiddlewareInterface[]
     */
    public function getMiddlewaresDefinitions(): array
    {
        $middlewares = [];

        foreach ($this->sprinkles as $sprinkle) {
            $sprinkleMiddlewares = array_map([$this, 'validateMiddleware'], $sprinkle::getMiddlewares());
            $middlewares = array_merge($middlewares, $sprinkleMiddlewares);
        }

        return $middlewares;
    }

    /**
     * Returns a list of available sprinkles.
     *
     * @return SprinkleReceipe[]
     */
    public function getSprinkles(): array
    {
        return $this->sprinkles;
    }

    /**
     * Get main sprinkle defined on construction.
     *
     * @return SprinkleReceipe
     */
    public function getMainSprinkle()
    {
        return $this->mainSprinkle;
    }

    /**
     * Returns a list of available sprinkle names.
     *
     * @return string[]
     */
    public function getSprinklesNames(): array
    {
        return array_map(function ($sprinkle) {
            return $sprinkle::getName();
        }, $this->sprinkles);
    }

    /**
     * Return if a Sprinkle is available
     * Can be used by other Sprinkles to test if their dependencies are met.
     *
     * @param string $sprinkle The class of the Sprinkle
     *
     * @return bool
     */
    public function isAvailable(string $sprinkle): bool
    {
        return in_array($sprinkle, $this->sprinkles);
    }

    /**
     * Return a list for the specified sprinkle and it's dependent, recursilvey.
     *
     * @param string $sprinkle Sprinkle to load, and it's dependent.
     *
     * @return SprinkleReceipe[]
     */
    protected function getDependentSprinkles(string $sprinkle): array
    {
        // Validate class
        if (!$this->validateClassIsSprinkleReceipe($sprinkle)) {
            $e = new SprinkleClassException();

            throw $e;
        }

        $sprinkles = [];

        // Merge dependent sprinkles
        foreach ($sprinkle::getSprinkles() as $dependent) {
            $sprinkles = array_merge($sprinkles, $this->getDependentSprinkles($dependent));
        }

        // Add top sprinkle to return
        $sprinkles[] = $sprinkle;

        // Remove duplicate and reindex
        $sprinkles = array_unique($sprinkles);
        $sprinkles = array_values($sprinkles);

        return $sprinkles;
    }

    /**
     * Validate the class implements SprinkleReceipe.
     *
     * @param string $class
     *
     * @return bool True/False if class implements SprinkleReceipe
     */
    protected function validateClassIsSprinkleReceipe(string $class): bool
    {
        if (!class_exists($class)) {
            return false;
        }

        $class = new ReflectionClass($class);
        if ($class->implementsInterface(SprinkleReceipe::class)) {
            return true;
        }

        return false;
    }

    /**
     * Validate command class string.
     *
     * @param string $command
     *
     * @throws NotFoundException
     * @throws BakeryClassException
     */
    protected function validateCommand(string $command): string
    {
        if (!class_exists($command)) {
            throw new NotFoundException('Bakery command `' . $command . '` not found.');
        }

        $class = new ReflectionClass($command);
        if (!$class->isSubclassOf(Command::class)) {
            throw new BakeryClassException('Bakery command `' . $command . '` must extends ' . Command::class);
        }

        return $command;
    }

    /**
     * Validate route file exist and return Closure the file contains.
     *
     * @param string $class
     *
     * @throws BadInstanceOfException
     */
    protected function validateRouteClass(string $class): RouteDefinitionInterface
    {
        // Get class instance
        $instance = new $class();

        // Class must be an instance of symfony command
        if (!$instance instanceof RouteDefinitionInterface) {
            throw new BadInstanceOfException('Routes definitions class `' . $instance::class . '` must be instance of ' . RouteDefinitionInterface::class);
        }

        return $instance;
    }

    /**
     * Validate container definition file exist and return it's array.
     *
     * @param string $class
     *
     * @throws BadInstanceOfException
     */
    protected function validateServicesProvider(string $class): ServicesProviderInterface
    {
        // Get class instance
        $instance = new $class();

        // Class must be an instance of symfony command
        if (!$instance instanceof ServicesProviderInterface) {
            throw new BadInstanceOfException('Services Provider `' . $instance::class . '` must be instance of ' . ServicesProviderInterface::class);
        }

        return $instance;
    }

    /**
     * Validate middleware class implement MiddlewareInterface and exist.
     *
     * @param string $middleware
     *
     * @throws NotFoundException
     * @throws BadInstanceOfException
     *
     * @return string
     */
    protected function validateMiddleware(string $middleware): string
    {
        if (!class_exists($middleware)) {
            throw new NotFoundException('Class `' . $middleware . '` not found.');
        }

        $class = new ReflectionClass($middleware);
        if (!$class->implementsInterface(MiddlewareInterface::class)) {
            throw new BadInstanceOfException('Middleware `' . $middleware . '` must be instance of ' . MiddlewareInterface::class);
        }

        return $middleware;
    }
}
