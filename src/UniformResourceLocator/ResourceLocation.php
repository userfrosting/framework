<?php

declare(strict_types=1);

/*
 * UserFrosting Framework (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/framework
 * @copyright Copyright (c) 2013-2021 Alexander Weissman, Louis Charette, Jordan Mele
 * @license   https://github.com/userfrosting/framework/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\UniformResourceLocator;

/**
 * The representation of a location
 */
class ResourceLocation implements ResourceLocationInterface
{
    /**
     * @param string      $name
     * @param string|null $path
     */
    public function __construct(
        protected string $name, 
        protected ?string $path = null
    ) {
        
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        if ($this->path === null) {
            return Normalizer::normalizePath($this->getName());
        }

        return Normalizer::normalizePath($this->path);
    }
}
