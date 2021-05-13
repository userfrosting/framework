<?php

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
interface SprinkleReceipe
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
     * Undocumented function.
     *
     * @return array[string]SprinkleReceipe
     */
    public function getBakeryCommands(): array;

    /**
     * Return dependent sprinkles.
     *
     * @return array[string]SprinkleReceipe
     */
    public function getSprinkles(): array;
}
