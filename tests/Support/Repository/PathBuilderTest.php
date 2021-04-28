<?php

/*
 * UserFrosting Framework (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/framework
 * @copyright Copyright (c) 2013-2021 Alexander Weissman, Louis Charette, Jordan Mele
 * @license   https://github.com/userfrosting/framework/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Tests\Support\Repository;

use PHPUnit\Framework\TestCase;
use UserFrosting\Support\Repository\PathBuilder\SimpleGlobBuilder;
use UserFrosting\Support\Repository\PathBuilder\StreamPathBuilder;
use UserFrosting\UniformResourceLocator\Normalizer;
use UserFrosting\UniformResourceLocator\ResourceLocator;

class PathBuilderTest extends TestCase
{
    protected $basePath;

    protected $locator;

    public function setUp(): void
    {
        $this->basePath = Normalizer::normalizePath(__DIR__.'/data');
        $this->locator = new ResourceLocator($this->basePath);

        $this->locator->registerStream('owls');

        // Add them one at a time to simulate how they are added in SprinkleManager
        $this->locator->registerLocation('core');
        $this->locator->registerLocation('account');
        $this->locator->registerLocation('admin');
    }

    public function testGlobBuildPaths()
    {
        // Arrange
        $builder = new SimpleGlobBuilder($this->locator, 'owls://');

        // Act
        $paths = $builder->buildPaths();

        // Assert
        $this->assertEquals($paths, [
            $this->basePath.'core/owls/megascops.php',
            $this->basePath.'core/owls/tyto.php',
            $this->basePath.'account/owls/megascops.php',
            $this->basePath.'admin/owls/megascops.php',
        ]);
    }

    public function testBuildPathsToFile()
    {
        // Arrange
        $builder = new StreamPathBuilder($this->locator, 'owls://megascops.php');

        // Act
        $paths = $builder->buildPaths();

        // Assert
        $this->assertEquals([
            $this->basePath.'core/owls/megascops.php',
            $this->basePath.'account/owls/megascops.php',
            $this->basePath.'admin/owls/megascops.php',
        ], $paths);
    }
}
