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
     *
     * @param string      $method             Method used to retrieve the registered instance from the Recipes.
     * @param string|null $recipeInterface    Interface the recipe must (optionally) implement.
     * @param string|null $extensionInterface Interface the registered must (optionally) implement.
     *
     * @throws NotFoundException      If $class is not found.
     * @throws BadInstanceOfException If $class doesn't implement $interface.
     *
     * @return mixed[]
     */
    public function getInstances(string $method, ?string $recipeInterface = null, ?string $extensionInterface = null): array
    {
        $instances = [];

        foreach ($this->sprinkleManager->getSprinkles() as $sprinkle) {
            $this->validateClass($sprinkle, $recipeInterface);
            foreach ($sprinkle::$method() as $extension) {
                $this->validateClass($extension, $extensionInterface);
                $instances[] = $this->ci->get($extension);
            }
        }

        return $instances;
    }

    /**
     * Validate the class implements the right interface.
     *
     * @param string      $class
     * @param string|null $interface
     *
     * @throws NotFoundException      If $class is not found.
     * @throws BadInstanceOfException If $class doesn't implement $interface.
     *
     * @return bool Return true if ok, throws error otherwise.
     */
    public function validateClass(string $class, ?string $interface = null): bool
    {
        if (!class_exists($class)) {
            throw new NotFoundException('Class `' . $class . '` not found.');
        }

        if (is_null($interface)) {
            return true;
        }

        if (!is_subclass_of($class, $interface)) {
            throw new BadInstanceOfException('Class `' . $class . '` must be instance of ' . $interface);
        }

        return true;
    }
}
