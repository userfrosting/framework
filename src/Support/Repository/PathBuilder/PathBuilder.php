<?php

/*
 * UserFrosting Support (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/support
 * @copyright Copyright (c) 2013-2019 Alexander Weissman
 * @license   https://github.com/userfrosting/support/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Support\Repository\PathBuilder;

use UserFrosting\UniformResourceLocator\ResourceLocatorInterface;

/**
 * Base PathBuilder class.
 *
 * @author Alexander Weissman (https://alexanderweissman.com)
 */
abstract class PathBuilder
{
    /**
     * @var ResourceLocatorInterface Locator service to use when searching for files.
     */
    protected $locator;

    /**
     * @var string Virtual path to search in the locator.
     */
    protected $uri;

    /**
     * Create the loader.
     *
     * @param ResourceLocatorInterface $locator
     * @param string                   $uri
     */
    public function __construct(ResourceLocatorInterface $locator, string $uri)
    {
        $this->locator = $locator;
        $this->uri = $uri;
    }

    /**
     * Build out the ordered list of file paths, using the designated locator and uri for this loader.
     *
     * @return array
     */
    abstract public function buildPaths();
}
