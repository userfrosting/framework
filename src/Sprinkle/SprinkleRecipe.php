<?php

declare(strict_types=1);

/*
 * UserFrosting Framework (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/framework
 * @copyright Copyright (c) 2013-2021 Alexander Weissman, Louis Charette, Jordan Mele
 * @license   https://github.com/userfrosting/framework/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Sprinkle;

/**
 * Sprinkle definition Interface.
 */
interface SprinkleRecipe
{
    /**
     * Return the Sprinkle name.
     *
     * @return string
     */
    public static function getName(): string;

    /**
     * Return the Sprinkle dir path.
     *
     * @return string
     */
    public static function getPath(): string;

    /**
     * Return an array of all registered Bakery Commands.
     *
     * @return array[string]SprinkleRecipe
     */
    public static function getBakeryCommands(): array;

    /**
     * Return dependent sprinkles.
     *
     * @return array[string]SprinkleRecipe
     */
    public static function getSprinkles(): array;

    /**
     * Returns a list of routes definition in PHP files.
     *
     * @return \UserFrosting\Routes\RouteDefinitionInterface[]
     */
    public static function getRoutes(): array;

    /**
     * Returns a list of all PHP-DI services/container definitions files.
     *
     * @return \UserFrosting\ServicesProvider\ServicesProviderInterface[]
     */
    public static function getServices(): array;

    /**
     * Returns a list of all Middlewares classes.
     *
     * @return \Psr\Http\Server\MiddlewareInterface[]
     */
    public static function getMiddlewares(): array;
}
