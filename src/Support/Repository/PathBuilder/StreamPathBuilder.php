<?php

/*
 * UserFrosting Framework (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/framework
 * @copyright Copyright (c) 2013-2021 Alexander Weissman, Louis Charette, Jordan Mele
 * @license   https://github.com/userfrosting/framework/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Support\Repository\PathBuilder;

/**
 * A builder class that adds all paths in the stream for the specified URI.
 *
 * @author Alexander Weissman (https://alexanderweissman.com)
 */
class StreamPathBuilder extends PathBuilder
{
    /**
     * Stack the resolved paths in each search path from the locator.
     *
     * @return array
     */
    public function buildPaths(): array
    {
        // Get all paths from the locator that match the uri.
        // Put them in reverse order to allow later files to override earlier files.
        return array_reverse($this->locator->findResources($this->uri, true, true));
    }
}
