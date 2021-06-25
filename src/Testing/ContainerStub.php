<?php

/*
 * UserFrosting Framework (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/framework
 * @copyright Copyright (c) 2013-2021 Alexander Weissman, Louis Charette, Jordan Mele
 * @license   https://github.com/userfrosting/framework/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Testing;

use DI\Container;
use DI\ContainerBuilder;

/**
 * Helper static method to create container stub for testing.
 */
class ContainerStub
{
    public static function create(): Container
    {
        $builder = new ContainerBuilder();
        $builder->useAnnotations(true);
        
        return $builder->build();
    }
}
