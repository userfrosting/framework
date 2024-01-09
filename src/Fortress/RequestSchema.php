<?php

/*
 * UserFrosting Framework (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/framework
 * @copyright Copyright (c) 2013-2021 Alexander Weissman, Louis Charette, Jordan Mele
 * @license   https://github.com/userfrosting/framework/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Fortress;

use UserFrosting\Fortress\RequestSchema\RequestSchemaRepository;
use UserFrosting\Support\Repository\Loader\FileRepositoryLoader;
use UserFrosting\Support\Repository\Loader\YamlFileLoader;

/**
 * Represents a schema for an HTTP request, compliant with the WDVSS standard
 * (https://github.com/alexweissman/wdvss).
 *
 * Same as \UserFrosting\Fortress\RequestSchema\RequestSchemaRepository, but
 * loads the schema from a file instead of an array.
 */
class RequestSchema extends RequestSchemaRepository
{
    /**
     * @var FileRepositoryLoader
     */
    protected FileRepositoryLoader $loader;

    /**
     * Loads the request schema from a file.
     *
     * @param string|null $path The full path to the file containing the [WDVSS schema](https://github.com/alexweissman/wdvss).
     */
    // TODO : Loader should be injected/injectable
    // TODO : Could accept both a path and an array of item, making it more flexible and Repository redundant?
    public function __construct(?string $path = null)
    {
        $items = [];

        if (!is_null($path)) {
            $this->loader = new YamlFileLoader($path);
            $items = $this->loader->load(false);
        }

        parent::__construct($items);
    }
}
