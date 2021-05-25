<?php

/*
 * UserFrosting Framework (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/framework
 * @copyright Copyright (c) 2013-2021 Alexander Weissman, Louis Charette, Jordan Mele
 * @license   https://github.com/userfrosting/framework/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\ServicesProvider;

interface ServicesProviderInterface
{
    /**
     * Returns list of injection definitions.
     *
     * @see https://php-di.org/doc/php-definitions.html#definition-types
     *
     * @return mixed[]
     */
    public function register(): array;
}
