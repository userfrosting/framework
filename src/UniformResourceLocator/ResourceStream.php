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
 * ResourceStream Class.
 *
 * The representation of a stream
 *
 * @author    Louis Charette
 */
class ResourceStream implements ResourceStreamInterface
{
    /**
     * @var string The path scheme
     */
    protected $scheme;

    /**
     * @var string The base path
     */
    protected $path;

    /**
     * @var string The stream prefix
     */
    protected $prefix;

    /**
     * @var bool Is the path shared? If yes, it won't be affected by locations
     */
    protected $shared;

    /**
     * Constructor.
     *
     * @param string $scheme
     * @param string $prefix
     * @param string $path
     * @param bool   $shared
     */
    // TODO : Use names arguments for prefix + type hint
    public function __construct($scheme, $prefix = '', $path = null, $shared = false)
    {
        if (is_null($path)) {
            $path = $scheme;
        }

        $this->setScheme($scheme);
        $this->setPrefix($prefix);
        $this->setPath($path);
        $this->setShared($shared);
    }

    /**
     * @return string
     */
    public function getScheme(): string
    {
        return $this->scheme;
    }

    /**
     * @param string $scheme
     *
     * @return static
     */
    public function setScheme($scheme): self
    {
        $this->scheme = $scheme;

        return $this;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path (default null)
     *
     * @return static
     */
    public function setPath($path): self
    {
        $this->path = Normalizer::normalizePath($path);

        return $this;
    }

    /**
     * @return string
     */
    public function getPrefix(): string
    {
        return $this->prefix;
    }

    /**
     * @param string $prefix
     *
     * @return static
     */
    public function setPrefix($prefix): self
    {
        $this->prefix = $prefix;

        return $this;
    }

    /**
     * @return bool
     */
    public function isShared(): bool
    {
        return $this->shared;
    }

    /**
     * @param bool $shared
     *
     * @return static
     */
    public function setShared($shared): self
    {
        $this->shared = $shared;

        return $this;
    }
}
