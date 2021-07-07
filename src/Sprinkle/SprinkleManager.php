<?php

/*
 * UserFrosting Framework (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/framework
 * @copyright Copyright (c) 2013-2021 Alexander Weissman, Louis Charette, Jordan Mele
 * @license   https://github.com/userfrosting/framework/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Sprinkle;

use ReflectionClass;
use Symfony\Component\Console\Command\Command;
use UserFrosting\Exceptions\BadInstanceOfException;
use UserFrosting\Exceptions\SprinkleClassException;
use UserFrosting\ServicesProvider\ServicesProviderInterface;

/**
 * Sprinkle Manager.
 *
 * Manages a collection of loaded Sprinkles for the application.
 * Returns all the information about the loaded Sprinkles, defined in each SprinkleRecipe.
 * This class does not perform any action on the application itself, it only serves information.
 * All routes, commands or other registration process is handled elsewhere.
 *
 * @property string $mainSprinkle
 */
class SprinkleManager
{
    /**
     * @var SprinkleRecipe[] List of available sprinkles
     */
    protected $sprinkles = [];

    /**
     * @var SprinkleRecipe Main sprinkle
     */
    protected $mainSprinkle;

    /**
     * Load sprinkles on construction.
     *
     * @param SprinkleRecipe $mainSprinkle
     */
    public function __construct($mainSprinkle)
    {
        $this->mainSprinkle = $mainSprinkle;
        $this->loadSprinkles();
    }

    /**
     * Generate the list of all loaded sprinkles through the main sprinkle dependencies.
     */
    public function loadSprinkles(): void
    {
        $this->sprinkles = $this->getDependentSprinkles($this->mainSprinkle);
    }

    /**
     * Returns a list of available sprinkles.
     *
     * @return SprinkleRecipe[]
     */
    public function getSprinkles(): array
    {
        return $this->sprinkles;
    }

    /**
     * Get main sprinkle defined on construction.
     *
     * @return SprinkleRecipe
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
     * Return a list for the specified sprinkle and it's dependent, recursively.
     *
     * @param string $sprinkle Sprinkle to load, and it's dependent.
     *
     * @return SprinkleRecipe[]
     */
    protected function getDependentSprinkles(string $sprinkle): array
    {
        // Validate class
        if (!$this->validateClassIsSprinkleRecipe($sprinkle)) {
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

        // Remove duplicate and re-index
        $sprinkles = array_unique($sprinkles);
        $sprinkles = array_values($sprinkles);

        return $sprinkles;
    }

    /**
     * Validate the class implements SprinkleRecipe.
     *
     * @param string $class
     *
     * @return bool True/False if class implements SprinkleRecipe
     */
    protected function validateClassIsSprinkleRecipe(string $class): bool
    {
        if (!class_exists($class)) {
            return false;
        }

        $class = new ReflectionClass($class);
        if ($class->implementsInterface(SprinkleRecipe::class)) {
            return true;
        }

        return false;
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
}
