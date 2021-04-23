<?php

/*
 * UserFrosting Framework (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/framework
 * @copyright Copyright (c) 2013-2021 Alexander Weissman, Louis Charette, Jordan Mele
 * @license   https://github.com/userfrosting/framework/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\UniformResourceLocator;

/**
 * ResourceStreamInterface Interface.
 *
 * @author    Louis Charette
 */
interface ResourceStreamInterface
{
    /**
     * @return string
     */
    public function getScheme(): string;

    /**
     * @param string $scheme
     *
     * @return static
     */
    public function setScheme($scheme);

    /**
     * @return string
     */
    public function getPath(): string;

    /**
     * @param string $path (default null)
     *
     * @return static
     */
    public function setPath($path);

    /**
     * @return string
     */
    public function getPrefix(): string;

    /**
     * @param string $prefix
     *
     * @return static
     */
    public function setPrefix($prefix);

    /**
     * @return bool
     */
    public function isShared(): bool;

    /**
     * @param bool $shared
     *
     * @return static
     */
    public function setShared($shared);
}
