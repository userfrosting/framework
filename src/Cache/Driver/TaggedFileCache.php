<?php

/*
 * UserFrosting Framework (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/framework
 * @copyright Copyright (c) 2013-2021 Alexander Weissman, Louis Charette, Jordan Mele
 * @license   https://github.com/userfrosting/framework/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Cache\Driver;

use Illuminate\Cache\TaggedCache;

/**
 * TaggedFileCache Class.
 *
 * Custom file based cache driver with supports for Tags
 * Inspired by unikent/taggedFileCache
 *
 * @author    Louis Charette
 */
class TaggedFileCache extends TaggedCache
{
    /**
     * {@inheritdoc}
     */
    public function taggedItemKey($key)
    {
        return $this->tags->getNamespace().$this->store->separator.$key;
    }
}
