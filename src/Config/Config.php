<?php

declare(strict_types=1);

/*
 * UserFrosting Framework (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/framework
 * @copyright Copyright (c) 2013-2021 Alexander Weissman, Louis Charette, Jordan Mele
 * @license   https://github.com/userfrosting/framework/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Config;

use UserFrosting\Support\Repository\Repository;

/**
 * Config Repository. Adds type specific getters.
 */
class Config extends Repository
{
    /**
     * Get the specified configuration value as bool.
     *
     * @param string    $key
     * @param bool|null $default
     *
     * @return bool
     */
    public function getBool(string $key, ?bool $default = null): bool
    {
        $value = $this->get($key, $default);

        if (!is_bool($value)) {
            throw new TypeException("Config key '$key' doesn't return bool.");
        }

        return $value;
    }

    /**
     * Get the specified configuration value as bool.
     *
     * @param string      $key
     * @param string|null $default
     *
     * @return string
     */
    public function getString(string $key, ?string $default = null): string
    {
        $value = $this->get($key, $default);

        if (!is_string($value)) {
            throw new TypeException("Config key '$key' doesn't return string.");
        }

        return $value;
    }

    /**
     * Get the specified configuration value as bool.
     *
     * @param string   $key
     * @param int|null $default
     *
     * @return int
     */
    public function getInt(string $key, ?int $default = null): int
    {
        $value = $this->get($key, $default);

        if (!is_int($value)) {
            throw new TypeException("Config key '$key' doesn't return int.");
        }

        return $value;
    }

    /**
     * Get the specified configuration value as bool.
     *
     * @param string       $key
     * @param mixed[]|null $default
     *
     * @return mixed[]
     */
    public function getArray(string $key, ?array $default = null): array
    {
        $value = $this->get($key, $default);

        if (!is_array($value)) {
            throw new TypeException("Config key '$key' doesn't return array.");
        }

        return $value;
    }
}
