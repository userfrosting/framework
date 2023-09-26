<?php

declare(strict_types=1);

/*
 * UserFrosting Framework (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/framework
 * @copyright Copyright (c) 2013-2021 Alexander Weissman, Louis Charette, Jordan Mele
 * @license   https://github.com/userfrosting/framework/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Cache;

use Illuminate\Contracts\Cache\Store;
use Illuminate\Filesystem\Filesystem;
use UserFrosting\Cache\Driver\TaggableFileStore as TaggableFileDriver;

/**
 * Setup a cache instance using the custom `TaggableFileDriver` driver
 */
class TaggableFileStore extends AbstractStore
{
    /**
     * Accept the file driver $path
     *
     * @param string $path      (default: "./")
     * @param string $separator (default: "")
     */
    public function __construct(
        protected $path = './',
        protected $separator = '~#~'
    ) {
    }

    /**
     * Create the custom TaggableFileDriver.
     */
    public function getStore(): Store
    {
        return new TaggableFileDriver(new Filesystem(), $this->path, [
            'separator' => $this->separator,
        ]);
    }
}
