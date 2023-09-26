<?php

declare(strict_types=1);

/*
 * UserFrosting Framework (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/framework
 * @copyright Copyright (c) 2013-2021 Alexander Weissman, Louis Charette, Jordan Mele
 * @license   https://github.com/userfrosting/framework/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Cache\Patch\Redis;

use Illuminate\Cache\RedisStore as UpstreamRedisStore;

/**
 * A patched `RedisStore` which resolves value an integer serialization bug.
 * See https://github.com/userfrosting/cache/issues/8.
 */
class RedisStore extends UpstreamRedisStore
{
    /** {@inheritdoc} */
    protected function serialize($value)
    {
        return serialize($value);
    }

    /** {@inheritdoc} */
    protected function unserialize($value)
    {
        return unserialize($value);
    }
}
