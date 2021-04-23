<?php

/*
 * UserFrosting Config (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/config
 * @copyright Copyright (c) 2013-2019 Alexander Weissman
 * @license   https://github.com/userfrosting/config/blob/master/LICENSE.md (MIT License)
 */

use PHPUnit\Framework\TestCase;
use UserFrosting\Config\ConfigPathBuilder;
use UserFrosting\UniformResourceLocator\ResourceLocator;

class ConfigPathBuilderTest extends TestCase
{
    protected $basePath;

    protected $locator;

    public function setUp(): void
    {
        $this->basePath = __DIR__.'/data';
        $this->locator = new ResourceLocator($this->basePath);

        // Add them as locations to simulate how they are added in SprinkleManager
        $this->locator->registerLocation('core');
        $this->locator->registerLocation('account');
        $this->locator->registerLocation('admin');
        $this->locator->registerStream('config');
    }

    public function testDefault()
    {
        // Arrange
        $builder = new ConfigPathBuilder($this->locator, 'config://');

        // Act
        $paths = $builder->buildPaths();

        $this->assertEquals([
            $this->basePath.'/core/config/default.php',
            $this->basePath.'/account/config/default.php',
            $this->basePath.'/admin/config/default.php',
        ], $paths);
    }

    public function testEnvironmentMode()
    {
        // Arrange
        $builder = new ConfigPathBuilder($this->locator, 'config://');

        // Act
        $paths = $builder->buildPaths('production');

        $this->assertEquals([
            $this->basePath.'/core/config/default.php',
            $this->basePath.'/core/config/production.php',
            $this->basePath.'/account/config/default.php',
            $this->basePath.'/account/config/production.php',
            $this->basePath.'/admin/config/default.php',
            $this->basePath.'/admin/config/production.php',
        ], $paths);
    }
}
