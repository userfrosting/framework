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
    public function getName(): string;

    /**
     * Return the Sprinkle dir path.
     *
     * @return string
     */
    public function getPath(): string;

    /**
     * Return dependent sprinkles.
     *
     * @return string[]|SprinkleRecipe[]
     */
    public function getSprinkles(): array;

    /**
     * Returns a list of routes definition in PHP files.
     *
     * @return string[]
     */
    public function getRoutes(): array;

    /**
     * Returns a list of all PHP-DI services/container definitions class.
     *
     * @return string[]|\UserFrosting\ServicesProvider\ServicesProviderInterface[]
     */
    public function getServices(): array;
}
