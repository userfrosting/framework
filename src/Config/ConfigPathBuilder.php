<?php

/*
 * UserFrosting Framework (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/framework
 * @copyright Copyright (c) 2013-2021 Alexander Weissman, Louis Charette, Jordan Mele
 * @license   https://github.com/userfrosting/framework/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Config;

use UserFrosting\Support\Repository\PathBuilder\PathBuilder;

/**
 * Config path builder, which builds a list of files for a given config environment.
 *
 * @author Alexander Weissman (https://alexanderweissman.com)
 *
 * @see http://blog.madewithlove.be/post/illuminate-config-v5/
 */
class ConfigPathBuilder extends PathBuilder
{
    /**
     * Add path to default.php and environment mode file, if specified.
     *
     * @param string|null $environment [defaul: null]
     *
     * @return array
     */
    public function buildPaths(?string $environment = null): array
    {
        // Get all paths from the locator that match the uri.
        // Put them in reverse order to allow later files to override earlier files.
        $searchPaths = array_reverse($this->locator->findResources($this->uri, true, true));

        $filePaths = [];
        foreach ($searchPaths as $path) {
            $cleanPath = rtrim($path, '/\\').'/';

            $filePaths[] = $cleanPath.'default.php';

            if (!is_null($environment)) {
                $filePaths[] = $cleanPath.$environment.'.php';
            }
        }

        return $filePaths;
    }
}
