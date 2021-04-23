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
use UserFrosting\Support\Repository\Loader\ArrayFileLoader;
use UserFrosting\UniformResourceLocator\ResourceLocator;

class ConfigTest extends TestCase
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

    public function testConfigDefault()
    {
        // Arrange
        $builder = new ConfigPathBuilder($this->locator, 'config://');
        $loader = new ArrayFileLoader($builder->buildPaths());

        // Act
        $data = $loader->load();

        $this->assertEquals($data, [
            'site' => [
                'AdminLTE' => [
                    'skin' => 'blue',
                ],
                'analytics' => [
                    'google' => [
                        'code'    => '',
                        'enabled' => false,
                    ],
                ],
                'author' => 'Author',
                'csrf'   => null,
                'debug'  => [
                    'ajax' => false,
                    'info' => true,
                ],
                'locales' => [
                    'available' => [
                        'en_US' => 'English',
                        'ar'    => 'العربية',
                        'fr_FR' => 'Français',
                        'pt_PT' => 'Português',
                        'de_DE' => 'Deutsch',
                        'th_TH' => 'ภาษาไทย',
                    ],
                    'default' => 'en_US',
                ],
                'title' => 'UserFrosting',
                'uri'   => [
                    'base' => [
                        'host'   => 'localhost',
                        'scheme' => 'http',
                        'port'   => null,
                        'path'   => 'myProject',
                    ],
                    'author'    => 'http://www.userfrosting.com',
                    'publisher' => '',
                ],
                'login' => [
                    'enable_email' => true,
                ],
                'registration' => [
                    'enabled'                    => true,
                    'captcha'                    => true,
                    'require_email_verification' => true,
                    'user_defaults'              => [
                        'locale' => 'en_US',
                        'group'  => 'terran',
                        'roles'  => [
                            'user' => true,
                        ],
                    ],
                ],
            ],
            'timezone' => 'America/New_York',
            'debug'    => [
                'auth' => true,
            ],
        ]);
    }

    public function testConfigEnvironmentMode()
    {
        // Arrange
        $builder = new ConfigPathBuilder($this->locator, 'config://');
        $loader = new ArrayFileLoader($builder->buildPaths('production'));

        // Act
        $data = $loader->load();

        $this->assertEquals($data, [
            'site' => [
                'AdminLTE' => [
                    'skin' => 'blue',
                ],
                'analytics' => [
                    'google' => [
                        'code'    => '',
                        'enabled' => true,
                    ],
                ],
                'author' => 'Author',
                'csrf'   => null,
                'debug'  => [
                    'ajax' => false,
                    'info' => false,
                ],
                'locales' => [
                    'available' => [
                        'en_US' => 'English',
                        'ar'    => 'العربية',
                        'fr_FR' => 'Français',
                        'pt_PT' => 'Português',
                        'de_DE' => 'Deutsch',
                        'th_TH' => 'ภาษาไทย',
                    ],
                    'default' => 'en_US',
                ],
                'title' => 'UserFrosting',
                'uri'   => [
                    'base' => [
                        'host'   => 'localhost',
                        'scheme' => 'http',
                        'port'   => null,
                        'path'   => 'myProject',
                    ],
                    'author'    => 'http://www.userfrosting.com',
                    'publisher' => '',
                ],
                'login' => [
                    'enable_email' => false,
                ],
                'registration' => [
                    'enabled'                    => false,
                    'captcha'                    => false,
                    'require_email_verification' => true,
                    'user_defaults'              => [
                        'locale' => 'en_US',
                        'group'  => 'terran',
                        'roles'  => [
                            'user' => true,
                        ],
                    ],
                ],
            ],
            'timezone' => 'America/New_York',
            'debug'    => [
                'auth' => false,
            ],
        ]);
    }
}
