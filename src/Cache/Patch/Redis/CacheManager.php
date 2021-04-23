<?php

/*
 * UserFrosting Framework (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/framework
 * @copyright Copyright (c) 2013-2021 Alexander Weissman, Louis Charette, Jordan Mele
 * @license   https://github.com/userfrosting/framework/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Cache\Patch\Redis;

use Illuminate\Cache\CacheManager as UpstreamCacheManager;

/**
 * Permits usage of patched `RedisStore`.
 * See https://github.com/userfrosting/cache/issues/8.
 */
class CacheManager extends UpstreamCacheManager
{
    protected function createRedisDriver(array $config)
    {
        $redis = $this->app['redis'];

        $connection = $config['connection'] ?? 'default';

        return $this->repository(new RedisStore($redis, $this->getPrefix($config), $connection));
    }
}
