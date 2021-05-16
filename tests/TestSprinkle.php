<?php

/*
 * UserFrosting Framework (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/framework
 * @copyright Copyright (c) 2013-2021 Alexander Weissman, Louis Charette, Jordan Mele
 * @license   https://github.com/userfrosting/framework/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Tests;

use UserFrosting\Sprinkle\SprinkleReceipe;

class TestSprinkle implements SprinkleReceipe
{
    /**
     * {@inheritDoc}
     */
    public static function getName(): string
    {
        return 'Test Sprinkle';
    }

    /**
     * {@inheritDoc}
     */
    public static function getPath(): string
    {
        return __DIR__ . '/data';
    }

    /**
     * {@inheritDoc}
     */
    public static function getBakeryCommands(): array
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public static function getSprinkles(): array
    {
        return [];
    }
}
