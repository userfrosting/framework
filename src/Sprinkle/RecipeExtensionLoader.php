<?php

/*
 * UserFrosting Framework (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/framework
 * @copyright Copyright (c) 2013-2021 Alexander Weissman, Louis Charette, Jordan Mele
 * @license   https://github.com/userfrosting/framework/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Sprinkle;

use DI\Container;
use UserFrosting\Exceptions\BadInstanceOfException;
use UserFrosting\Support\Exception\NotFoundException;

/**
 * Class service used to load recipe extensions.
 * It make it easier for other sprinkles to implement their own registration method in the Recipe.
 * Class can be loaded through Container with dependencies injected.
 */
class RecipeExtensionLoader
{
    public function __construct(
        protected SprinkleManager $sprinkleManager,
        protected Container $ci
    ) {
    }

    /**
     * Get registered instances from all Sprinkles, recursively.
     * The instances will be initiated by the Dependencies Injection.
     *
     * @param string      $method             Method used to retrieve the registered instance from the Recipes.
     * @param string|null $recipeInterface    Interface the sprinkle recipe must (optionally) implement. Sprinkle Recipe that don't implement this interface will be ignored.
     * @param string|null $extensionInterface Interface the registered must (optionally) implement.
     * @param bool        $throwBadInterface  If true, will throws BadInstanceOfException if Sprinkle Recipe don't implement $recipeInterface. Sprinkle will be ignored if false (default).
     *
     * @throws NotFoundException      If a $class is not found.
     * @throws BadInstanceOfException If a $class doesn't implement the $interface.
     *
     * @return mixed[]
     */
    public function getInstances(string $method, ?string $recipeInterface = null, ?string $extensionInterface = null, bool $throwBadInterface = false): array
    {
        $instances = [];

        foreach ($this->sprinkleManager->getSprinkles() as $sprinkle) {
            if (!$this->validateClass($sprinkle, $recipeInterface, $throwBadInterface)) {
                continue;
            }
            foreach ($sprinkle::$method() as $extension) {
                $this->validateClass($extension, $extensionInterface, true);
                $instances[] = $this->ci->get($extension);
            }
        }

        return $instances;
    }

    /**
     * Get registered objects from all Sprinkles, recursively.
     * The objects WON'T be initiated by the Dependencies Injection.
     *
     * @param string      $method             Method used to retrieve the registered instance from the Recipes.
     * @param string|null $recipeInterface    Interface the sprinkle recipe must (optionally) implement. Sprinkle Recipe that don't implement this interface will be ignored.
     * @param string|null $extensionInterface Interface the object must (optionally) implement.
     * @param bool        $throwBadInterface  If true, will throws BadInstanceOfException if Sprinkle Recipe don't implement $recipeInterface. Sprinkle will be ignored if false (default).
     *
     * @throws NotFoundException      If sprinkle $class is not found.
     * @throws BadInstanceOfException If sprinkle $class doesn't implement the $interface.
     *
     * @return mixed[]
     */
    public function getObjects(string $method, ?string $recipeInterface = null, ?string $extensionInterface = null, bool $throwBadInterface = false): array
    {
        $collection = [];

        foreach ($this->sprinkleManager->getSprinkles() as $sprinkle) {
            if (!$this->validateClass($sprinkle, $recipeInterface, $throwBadInterface)) {
                continue;
            }
            $objects = $sprinkle::$method();
            foreach ($objects as $object) {
                if (!is_null($extensionInterface) && !is_subclass_of($object, $extensionInterface)) {
                    throw new BadInstanceOfException('Object must be instance of ' . $extensionInterface);
                }

                $collection[] = $object;
            }
        }

        return $collection;
    }

    /**
     * Validate the class implements the right interface.
     *
     * @param string      $class
     * @param string|null $interface
     * @param bool        $throwBadInterface Throws BadInstanceOfException if true, otherwise return false.
     *
     * @throws NotFoundException      If $class is not found.
     * @throws BadInstanceOfException If $class doesn't implement $interface.
     *
     * @return bool Return true if ok, throws error otherwise.
     */
    public function validateClass(string $class, ?string $interface = null, bool $throwBadInterface = false): bool
    {
        if (!class_exists($class)) {
            throw new NotFoundException('Class `' . $class . '` not found.');
        }

        if (is_null($interface)) {
            return true;
        }

        if (!is_subclass_of($class, $interface)) {
            if ($throwBadInterface) {
                throw new BadInstanceOfException('Class `' . $class . '` must be instance of ' . $interface);
            } else {
                return false;
            }
        }

        return true;
    }
}
