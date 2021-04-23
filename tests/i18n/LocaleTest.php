<?php

/*
 * UserFrosting i18n (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/i18n
 * @copyright Copyright (c) 2013-2019 Alexander Weissman, Louis Charette
 * @license   https://github.com/userfrosting/i18n/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\I18n\Tests;

use PHPUnit\Framework\TestCase;
use UserFrosting\I18n\Locale;
use UserFrosting\I18n\LocaleInterface;
use UserFrosting\Support\Exception\FileNotFoundException;
use UserFrosting\UniformResourceLocator\ResourceLocator;

class LocaleTest extends TestCase
{
    /** @var string */
    protected $basePath;

    /** @var ResourceLocator */
    protected $locator;

    public function setUp(): void
    {
        $this->basePath = __DIR__.'/data/sprinkles';
        $this->locator = new ResourceLocator($this->basePath);
        $this->locator->registerStream('locale');

        // Add locations one at a time to simulate how they are added in SprinkleManager
        $this->locator->registerLocation('core');
        $this->locator->registerLocation('account');
        $this->locator->registerLocation('fr_CA');
    }

    public function testConstructor(): Locale
    {
        $locale = new Locale('fr_FR');
        $this->assertInstanceOf(LocaleInterface::class, $locale);

        return $locale;
    }

    public function testConstructorWithNotFoundPath(): void
    {
        $this->expectException(FileNotFoundException::class);
        new Locale('fr_FR', 'locale://fr_FR/dontexist.yaml');
    }

    /**
     * @depends testConstructor
     */
    public function testGetConfigFile(Locale $locale): void
    {
        $data = $locale->getConfigFile();
        $this->assertIsString($data);

        $this->assertSame('locale://fr_FR/locale.yaml', $data);
    }

    /**
     * @depends testGetConfigFile
     */
    public function testConstructorWithNotPath(): void
    {
        $locale = new Locale('fr_FR');
        $this->assertInstanceOf(LocaleInterface::class, $locale);
        $this->assertSame('locale://fr_FR/locale.yaml', $locale->getConfigFile());
    }

    /**
     * @depends testConstructor
     */
    public function testGetIdentifier(Locale $locale): void
    {
        $data = $locale->getIdentifier();
        $this->assertIsString($data);

        $this->assertSame('fr_FR', $data);
    }

    /**
     * @depends testConstructor
     */
    public function testGetConfig(Locale $locale): void
    {
        $data = $locale->getConfig();
        $this->assertIsArray($data);

        $this->assertSame([
            'name'           => 'French',
            'regional'       => 'Français',
            'authors'        => [
                'Foo Bar',
                'Bar Foo', // Not available in `core` version
            ],
            'plural_rule' => 2,
            'parents'     => [
                'en_US',
            ],
        ], $data);
    }

    /**
     * @depends testConstructor
     * @depends testGetConfig
     */
    public function testGetAuthors(Locale $locale): void
    {
        $data = $locale->getAuthors();
        $this->assertIsArray($data);

        $this->assertSame([
            'Foo Bar',
            'Bar Foo', // Not available in `core` version
        ], $data);

        $this->assertSame($locale->getConfig()['authors'], $data);
    }

    /**
     * @depends testConstructor
     * @depends testGetConfig
     */
    public function testGetDetails(Locale $locale): void
    {
        //getName
        $this->assertIsString($locale->getName());
        $this->assertSame('French', $locale->getName());

        //getRegionalName
        $this->assertIsString($locale->getRegionalName());
        $this->assertSame('Français', $locale->getRegionalName());

        //getDependentLocalesIdentifier
        $this->assertIsArray($locale->getDependentLocalesIdentifier());
        $this->assertSame(['en_US'], $locale->getDependentLocalesIdentifier());
    }

    /**
     * @depends testGetDetails
     */
    public function testGetLocalizedNameWithNoLocalizedConfig(): void
    {
        $locale = new Locale('es_ES');
        $this->assertSame('Spanish', $locale->getRegionalName());
    }

    /**
     * @depends testConstructor
     * @depends testGetConfig
     */
    public function testGetPluralRule(Locale $locale): void
    {
        $this->assertIsInt($locale->getPluralRule());
        $this->assertSame(2, $locale->getPluralRule());
    }

    /**
     * @depends testConstructorWithNotPath
     * @depends testGetPluralRule
     */
    public function testGetPluralRuleWithNoRule(): void
    {
        $locale = new Locale('es_ES');
        $this->assertIsInt($locale->getPluralRule());
        $this->assertSame(1, $locale->getPluralRule());
    }

    /**
     * @depends testGetDetails
     * @depends testGetAuthors
     */
    public function testGetDetailsWithInheritance(): void
    {
        $locale = new Locale('fr_CA');

        //getName
        $this->assertIsString($locale->getName());
        $this->assertSame('French Canadian', $locale->getName());

        //getRegionalName
        $this->assertIsString($locale->getRegionalName());
        $this->assertSame('Français Canadien', $locale->getRegionalName());

        //getDependentLocalesIdentifier
        $this->assertIsArray($locale->getDependentLocalesIdentifier());
        $this->assertSame(['fr_FR'], $locale->getDependentLocalesIdentifier());

        //getAuthors
        $this->assertSame(['Foo Bar', 'Bar Foo'], $locale->getAuthors());
    }

    /**
     * @depends testConstructorWithNotPath
     * @depends testGetPluralRule
     */
    public function testGetPluralRuleWithInheritance(): void
    {
        $locale = new Locale('fr_CA');
        $this->assertIsInt($locale->getPluralRule());
        $this->assertSame(2, $locale->getPluralRule());
    }

    /**
     * @depends testConstructor
     * @depends testGetDetails
     */
    public function testGetDependentLocales(Locale $locale): void
    {
        $result = $locale->getDependentLocales();
        $this->assertIsArray($result);
        $this->assertInstanceOf(LocaleInterface::class, $result[0]);
    }

    /**
     * @depends testGetDependentLocales
     */
    public function testGetDependentLocalesWithNullParent(): void
    {
        $locale = new Locale('es_ES');

        $result = $locale->getDependentLocales();
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testConstructorWithCustomFile(): void
    {
        $locale = new Locale('de_DE', 'locale://de_DE/foo.yaml');
        $this->assertInstanceOf(LocaleInterface::class, $locale);

        $this->assertSame([], $locale->getAuthors());
        $this->assertSame('locale://de_DE/foo.yaml', $locale->getConfigFile());
        $this->assertSame('de_DE', $locale->getIdentifier());
        $this->assertSame([], $locale->getConfig());
        $this->assertSame([], $locale->getDependentLocales());
        $this->assertSame([], $locale->getDependentLocalesIdentifier());
        $this->assertSame('', $locale->getName());
        $this->assertSame(1, $locale->getPluralRule());
        $this->assertSame('de_DE', $locale->getRegionalName());
    }

    /**
     * @see https://github.com/userfrosting/UserFrosting/issues/1133
     *
     * @dataProvider locationProvider
     */
    public function testWithSharedLocation(string $path): void
    {
        $locator = new ResourceLocator(__DIR__);
        $locator->registerStream('locale', '', $path, true);

        $locale = new Locale('fr_FR');
        $this->assertInstanceOf(LocaleInterface::class, $locale);
        $this->assertSame('Tomato', $locale->getName());

        $locale = new Locale('en_US');
        $this->assertInstanceOf(LocaleInterface::class, $locale);
        $this->assertSame('English', $locale->getName());
    }

    /**
     * @return string[]
     */
    public function locationProvider(): array
    {
        return [
            [__DIR__.'/data/shared'],
            ['data/shared'],
        ];
    }
}
