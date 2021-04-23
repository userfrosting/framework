<?php

/*
 * UserFrosting Framework (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/framework
 * @copyright Copyright (c) 2013-2021 Alexander Weissman, Louis Charette, Jordan Mele
 * @license   https://github.com/userfrosting/framework/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Support\Repository\Loader;

/**
 * Load files from a PHP array.
 *
 * @author Alexander Weissman (https://alexanderweissman.com)
 */
class ArrayFileLoader extends FileRepositoryLoader
{
    /**
     * {@inheritdoc}
     */
    protected function parseFile(string $path): array
    {
        return require $path;
    }
}
