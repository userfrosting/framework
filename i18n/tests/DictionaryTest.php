<?php

/*
 * UserFrosting i18n (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/i18n
 * @copyright Copyright (c) 2013-2019 Alexander Weissman, Louis Charette
 * @license   https://github.com/userfrosting/i18n/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\I18n\Tests;

use Mockery;
use PHPUnit\Framework\TestCase;
use UserFrosting\I18n\Dictionary;
use UserFrosting\I18n\DictionaryInterface;
use UserFrosting\I18n\Locale;
use UserFrosting\I18n\LocaleInterface;
use UserFrosting\Support\Repository\Loader\ArrayFileLoader;
use UserFrosting\UniformResourceLocator\Resource;
use UserFrosting\UniformResourceLocator\ResourceLocator;

class DictionaryTest extends TestCase
{
    protected $basePath;

    protected $locator;

    public function setUp(): void
    {
        $this->basePath = __DIR__.'/data/dictionary';
        $this->locator = new ResourceLocator($this->basePath);

        $this->locator->registerStream('locale', '', null, true);
    }

    public function tearDown(): void
    {
        Mockery::close();
    }

    public function testConstructor(): void
    {
        $locale = Mockery::mock(Locale::class);

        $dictionary = new Dictionary($locale, $this->locator);
        $this->assertInstanceOf(DictionaryInterface::class, $dictionary);
    }

    /**
     * @depends testConstructor
     */
    public function testGetLocale(): void
    {
        $locale = Mockery::mock(Locale::class);
        $locator = Mockery::mock(ResourceLocator::class);
        $dictionary = new Dictionary($locale, $locator); //<-- Test no fileLoader too

        // Make sure constructor works
        $this->assertInstanceOf(DictionaryInterface::class, $dictionary);
        $this->assertInstanceOf(LocaleInterface::class, $dictionary->getLocale());
    }

    /**
     * @depends testConstructor
     */
    public function testGetDictionary_withNoDependentLocaleNoData(): void
    {
        // Prepare mocked locale - aa_bb
        $locale = Mockery::mock(Locale::class);
        $locale->shouldReceive('getDependentLocales')->andReturn([]);
        $locale->shouldReceive('getDependentLocalesIdentifier')->andReturn([]);
        $locale->shouldReceive('getIdentifier')->andReturn('aa_bb');

        // Prepare mock Locator - Return no file
        $locator = Mockery::mock(ResourceLocator::class);
        $locator->shouldReceive('listResources')->with('locale://aa_bb', true)->andReturn([]);

        // Prepare mock FileLoader - No files, so loader shouldn't load anything
        $fileLoader = Mockery::mock(ArrayFileLoader::class);
        $fileLoader->shouldNotReceive('setPaths');
        $fileLoader->shouldNotReceive('load');

        // Set expectations
        $expectedResult = [];

        // Get dictionary
        $dictionary = new Dictionary($locale, $locator, $fileLoader);
        $data = $dictionary->getDictionary();

        // Perform assertions
        $this->assertIsArray($data);
        $this->assertEquals($expectedResult, $data);
    }

    /**
     * @depends testGetDictionary_withNoDependentLocaleNoData
     */
    public function testSetUri(): void
    {
        // Prepare mocked locale - aa_bb
        $locale = Mockery::mock(Locale::class);
        $locale->shouldReceive('getDependentLocales')->andReturn([]);
        $locale->shouldReceive('getDependentLocalesIdentifier')->andReturn([]);
        $locale->shouldReceive('getIdentifier')->andReturn('aa_bb');

        // Prepare mock Locator - Return no file
        $locator = Mockery::mock(ResourceLocator::class);
        $locator->shouldReceive('listResources')->with('foo://aa_bb', true)->andReturn([]);

        // Prepare mock FileLoader - No files, so loader shouldn't load anything
        $fileLoader = Mockery::mock(ArrayFileLoader::class);
        $fileLoader->shouldNotReceive('setPaths');
        $fileLoader->shouldNotReceive('load');

        // Set expectations
        $expectedResult = [];

        // Get dictionary
        $dictionary = new Dictionary($locale, $locator, $fileLoader);
        $dictionary->setUri('foo://');
        $data = $dictionary->getDictionary();

        // Perform assertions
        $this->assertIsArray($data);
        $this->assertEquals($expectedResult, $data);
    }

    /**
     * @depends testGetDictionary_withNoDependentLocaleNoData
     */
    public function testGetDictionary_withNoDependentLocaleWithData(): void
    {
        // Set expectations
        $expectedResult = ['Foo' => 'Bar'];

        // Prepare mocked locale - aa_bb
        $locale = Mockery::mock(Locale::class);
        $locale->shouldReceive('getDependentLocales')->andReturn([]);
        $locale->shouldReceive('getDependentLocalesIdentifier')->andReturn([]);
        $locale->shouldReceive('getIdentifier')->andReturn('aa_bb');

        // Prepare mock Resource - File `Foo/Bar/File1.php`
        $file = Mockery::mock(Resource::class);
        $file->shouldReceive('getExtension')->andReturn('php');
        $file->shouldReceive('__toString')->andReturn('Foo/Bar/File1.php');

        // Prepare mock Locator - Return the file
        $locator = Mockery::mock(ResourceLocator::class);
        $locator->shouldReceive('listResources')->with('locale://aa_bb', true)->andReturn([$file]);

        // Prepare mock FileLoader - Will return the mock file, with a mock data
        $fileLoader = Mockery::mock(ArrayFileLoader::class);
        $fileLoader->shouldReceive('setPaths')->with(['Foo/Bar/File1.php']);
        $fileLoader->shouldReceive('load')->andReturn($expectedResult);

        // Get dictionary
        $dictionary = new Dictionary($locale, $locator, $fileLoader);
        $data = $dictionary->getDictionary();

        // Perform assertions
        $this->assertIsArray($data);
        $this->assertEquals($expectedResult, $data);
    }

    /**
     * @depends testGetDictionary_withNoDependentLocaleWithData
     */
    public function testGetDictionary_withNoDependentLocaleWithManyFiles(): void
    {
        // Set expectations
        $expectedResult = [
            'Foo'  => 'Bar',
            'Bar'  => 'Foo',
            'test' => [
                'Bar' => 'Rab',
                'Foo' => 'Oof',
            ],
        ];

        // Prepare mocked locale - aa_bb
        $locale = Mockery::mock(Locale::class);
        $locale->shouldReceive('getDependentLocales')->once()->andReturn([]);
        $locale->shouldReceive('getIdentifier')->twice()->andReturn('aa_bb');

        // Prepare first mock Resource - File `Foo/Bar/File1.php`
        $file1 = Mockery::mock(Resource::class);
        $file1->shouldReceive('getExtension')->once()->andReturn('php');
        $file1->shouldReceive('__toString')->once()->andReturn('Foo/Bar/File1.php');

        // Prepare second mock Resource - File `Bar/Foo/File2.php`
        $file2 = Mockery::mock(Resource::class);
        $file2->shouldReceive('getExtension')->once()->andReturn('php');
        $file2->shouldReceive('__toString')->once()->andReturn('Bar/Foo/File2.php');

        // Prepare Third mock Resource - non `.php` file
        $file3 = Mockery::mock(Resource::class);
        $file3->shouldReceive('getExtension')->once()->andReturn('txt');
        $file3->shouldNotReceive('__toString');

        // Prepare mock Locator - Return the file
        $locator = Mockery::mock(ResourceLocator::class);
        $locator->shouldReceive('listResources')->with('locale://aa_bb', true)->once()->andReturn([$file1, $file2, $file3]);

        // Prepare mock FileLoader - Will return the mock file, with a mock data
        $fileLoader = Mockery::mock(ArrayFileLoader::class);
        $fileLoader->shouldReceive('setPaths')->once();
        $fileLoader->shouldReceive('load')->once()->andReturn($expectedResult);

        // Get dictionary
        $dictionary = new Dictionary($locale, $locator, $fileLoader);
        $data = $dictionary->getDictionary();

        // Perform assertions
        $this->assertIsArray($data);
        $this->assertEquals($expectedResult, $data);
    }

    /**
     * @depends testGetDictionary_withNoDependentLocaleNoData
     */
    public function testGetDictionary_withDependentLocaleNoDataOnBoth(): void
    {
        // Prepare dependent mocked locale - ff_FF
        $localeDependent = Mockery::mock(Locale::class);
        $localeDependent->shouldReceive('getDependentLocales')->andReturn([]);
        $localeDependent->shouldReceive('getDependentLocalesIdentifier')->andReturn([]);
        $localeDependent->shouldReceive('getIdentifier')->andReturn('ff_FF');

        // Prepare mocked locale - aa_bb
        $locale = Mockery::mock(Locale::class);
        $locale->shouldReceive('getDependentLocales')->andReturn([$localeDependent]);
        $locale->shouldReceive('getDependentLocalesIdentifier')->andReturn(['ff_FF']);
        $locale->shouldReceive('getIdentifier')->andReturn('aa_bb');

        // Prepare mock Locator - Return no file
        $locator = Mockery::mock(ResourceLocator::class);
        $locator->shouldReceive('listResources')->once()->with('locale://aa_bb', true)->andReturn([]);
        $locator->shouldReceive('listResources')->once()->with('locale://ff_FF', true)->andReturn([]);

        // Prepare mock FileLoader - No files, so loader shouldn't load anything
        $fileLoader = Mockery::mock(ArrayFileLoader::class);
        $fileLoader->shouldNotReceive('setPaths');
        $fileLoader->shouldNotReceive('load');

        // Set expectations
        $expectedResult = [];

        // Get dictionary
        $dictionary = new Dictionary($locale, $locator, $fileLoader);
        $data = $dictionary->getDictionary();

        // Perform assertions
        $this->assertIsArray($data);
        $this->assertEquals($expectedResult, $data);
    }

    /**
     * @depends testGetDictionary_withDependentLocaleNoDataOnBoth
     */
    public function testGetDictionary_withDependentLocaleAndDataOnAA(): void
    {
        // Set expectations
        $expectedResult = [
            'Foo'  => 'Bar',
            'test' => [
                'aaa' => 'AAA',
                'ccc' => '',
            ],
        ];

        // Prepare dependent mocked locale - ff_FF
        $localeDependent = Mockery::mock(Locale::class);
        $localeDependent->shouldReceive('getDependentLocales')->andReturn([]);
        $localeDependent->shouldReceive('getDependentLocalesIdentifier')->andReturn([]);
        $localeDependent->shouldReceive('getIdentifier')->andReturn('ff_FF');

        // Prepare mocked locale - aa_bb
        $locale = Mockery::mock(Locale::class);
        $locale->shouldReceive('getDependentLocales')->andReturn([$localeDependent]);
        $locale->shouldReceive('getDependentLocalesIdentifier')->andReturn(['ff_FF']);
        $locale->shouldReceive('getIdentifier')->andReturn('aa_bb');

        // Prepare first mock Resource - File `Foo/Bar/File1.php`
        $file1 = Mockery::mock(Resource::class);
        $file1->shouldReceive('getExtension')->andReturn('php');
        $file1->shouldReceive('__toString')->andReturn('Foo/Bar/File1.php');

        // Prepare mock Locator - Return no file on ff_FF
        $locator = Mockery::mock(ResourceLocator::class);
        $locator->shouldReceive('listResources')->once()->with('locale://aa_bb', true)->andReturn([$file1]);
        $locator->shouldReceive('listResources')->once()->with('locale://ff_FF', true)->andReturn([]);

        // Prepare mock FileLoader - No files, so loader shouldn't load anything
        $fileLoader = Mockery::mock(ArrayFileLoader::class);
        $fileLoader->shouldReceive('setPaths')->with(['Foo/Bar/File1.php']);
        $fileLoader->shouldReceive('load')->andReturn($expectedResult);

        // Get dictionary
        $dictionary = new Dictionary($locale, $locator, $fileLoader);
        $data = $dictionary->getDictionary();

        // Perform assertions
        $this->assertIsArray($data);
        $this->assertEquals($expectedResult, $data);
    }

    /**
     * @depends testGetDictionary_withDependentLocaleAndDataOnAA
     */
    public function testGetDictionary_withDependentLocaleAndDataOnFF(): void
    {
        // Set expectations
        $expectedResult = [
            'Bar'  => 'Foo',
            'test' => [
                'bbb' => 'BBB',
                'ccc' => 'CCC',
            ],
        ];

        // Prepare dependent mocked locale - ff_FF
        $localeDependent = Mockery::mock(Locale::class);
        $localeDependent->shouldReceive('getDependentLocales')->andReturn([]);
        $localeDependent->shouldReceive('getDependentLocalesIdentifier')->andReturn([]);
        $localeDependent->shouldReceive('getIdentifier')->andReturn('ff_FF');

        // Prepare mocked locale - aa_bb
        $locale = Mockery::mock(Locale::class);
        $locale->shouldReceive('getDependentLocales')->andReturn([$localeDependent]);
        $locale->shouldReceive('getDependentLocalesIdentifier')->andReturn(['ff_FF']);
        $locale->shouldReceive('getIdentifier')->andReturn('aa_bb');

        // Prepare first mock Resource - File `Foo/Bar/File1.php`
        $file1 = Mockery::mock(Resource::class);
        $file1->shouldReceive('getExtension')->andReturn('php');
        $file1->shouldReceive('__toString')->andReturn('Bar/Foo/File2.php');

        // Prepare mock Locator - Return no file on ff_FF
        $locator = Mockery::mock(ResourceLocator::class);
        $locator->shouldReceive('listResources')->once()->with('locale://aa_bb', true)->andReturn([]);
        $locator->shouldReceive('listResources')->once()->with('locale://ff_FF', true)->andReturn([$file1]);

        // Prepare mock FileLoader - No files, so loader shouldn't load anything
        $fileLoader = Mockery::mock(ArrayFileLoader::class);
        $fileLoader->shouldReceive('setPaths')->with(['Bar/Foo/File2.php']);
        $fileLoader->shouldReceive('load')->andReturn($expectedResult);

        // Get dictionary
        $dictionary = new Dictionary($locale, $locator, $fileLoader);
        $data = $dictionary->getDictionary();

        // Perform assertions
        $this->assertIsArray($data);
        $this->assertEquals($expectedResult, $data);
    }

    /**
     * @depends testGetDictionary_withDependentLocaleAndDataOnFF
     */
    public function testGetDictionary_withDependentLocaleDataOnBoth(): void
    {
        // Set expectations
        $fr_FR_FILE = [
            'Foo'  => 'Bar',
            'test' => [
                'aaa' => 'AAA',
                'ccc' => '', // Overwrites "CCC"
                'ddd' => 'DDD', // Overwrites ""
            ],
        ];
        $en_US_FILE = [
            'Bar'  => 'Foo',
            'test' => [
                'bbb' => 'BBB',
                'ccc' => 'CCC', // Overwriten by ""
                'ddd' => '', //Overwriten by "DDD"
            ],
        ];

        // NOTE : FF is a parent of AA. So FF should be loaded first
        $expectedResult = [
            'Foo'  => 'Bar',
            'test' => [
                'aaa' => 'AAA',
                'ccc' => '',
                'ddd' => 'DDD',
                'bbb' => 'BBB',
            ],
            'Bar'  => 'Foo',
        ];

        // Prepare dependent mocked locale - en_US
        $localeDependent = Mockery::mock(Locale::class);
        $localeDependent->shouldReceive('getDependentLocales')->andReturn([]);
        $localeDependent->shouldReceive('getDependentLocalesIdentifier')->andReturn([]);
        $localeDependent->shouldReceive('getIdentifier')->andReturn('en_US');

        // Prepare mocked locale - fr_FR
        $locale = Mockery::mock(Locale::class);
        $locale->shouldReceive('getDependentLocales')->andReturn([$localeDependent]);
        $locale->shouldReceive('getDependentLocalesIdentifier')->andReturn(['en_US']);
        $locale->shouldReceive('getIdentifier')->andReturn('fr_FR');

        // Prepare first mock Resource - File `Foo/Bar/File1.php`
        $file_FR = Mockery::mock(Resource::class);
        $file_FR->shouldReceive('getExtension')->andReturn('php');
        $file_FR->shouldReceive('__toString')->andReturn('Locale/fr_FR/file.php');

        // Prepare first mock Resource - File `Foo/Bar/File1.php`
        $file_EN = Mockery::mock(Resource::class);
        $file_EN->shouldReceive('getExtension')->andReturn('php');
        $file_EN->shouldReceive('__toString')->andReturn('Locale/en_US/file.php');

        // Prepare mock Locator - Return no file on ff_FF
        $locator = Mockery::mock(ResourceLocator::class);
        $locator->shouldReceive('listResources')->once()->with('locale://fr_FR', true)->andReturn([$file_FR]);
        $locator->shouldReceive('listResources')->once()->with('locale://en_US', true)->andReturn([$file_EN]);

        // Prepare mock FileLoader - No files, so loader shouldn't load anything
        $fileLoader = Mockery::mock(ArrayFileLoader::class);
        $fileLoader->shouldReceive('setPaths')->once()->with(['Locale/fr_FR/file.php']);
        $fileLoader->shouldReceive('load')->once()->andReturn($fr_FR_FILE);

        $fileLoader->shouldReceive('setPaths')->once()->with(['Locale/en_US/file.php']);
        $fileLoader->shouldReceive('load')->once()->andReturn($en_US_FILE);

        // Get dictionary
        $dictionary = new Dictionary($locale, $locator, $fileLoader);
        $data = $dictionary->getDictionary();

        // Perform assertions
        $this->assertIsArray($data);
        $this->assertEquals($expectedResult, $data);
    }

    /**
     * @depends testGetDictionary_withDependentLocaleNoDataOnBoth
     */
    public function testGetDictionary_withManyDependentLocale(): void
    {
        // Prepare dependent mocked locale - ee_EE
        $localeSubDependent = Mockery::mock(Locale::class);
        $localeSubDependent->shouldReceive('getDependentLocales')->andReturn([]);
        $localeSubDependent->shouldReceive('getDependentLocalesIdentifier')->andReturn([]);
        $localeSubDependent->shouldReceive('getIdentifier')->andReturn('ee_EE');

        // Prepare dependent mocked locale - ff_FF
        $localeDependent = Mockery::mock(Locale::class);
        $localeDependent->shouldReceive('getDependentLocales')->andReturn([$localeSubDependent]);
        $localeDependent->shouldReceive('getDependentLocalesIdentifier')->andReturn(['ee_EE']);
        $localeDependent->shouldReceive('getIdentifier')->andReturn('ff_FF');

        // Prepare mocked locale - aa_bb
        $locale = Mockery::mock(Locale::class);
        $locale->shouldReceive('getDependentLocales')->andReturn([$localeDependent]);
        $locale->shouldReceive('getDependentLocalesIdentifier')->andReturn(['ff_FF']);
        $locale->shouldReceive('getIdentifier')->andReturn('aa_bb');

        // Prepare mock Locator - Return no file on ff_FF
        $locator = Mockery::mock(ResourceLocator::class);
        $locator->shouldReceive('listResources')->once()->with('locale://aa_bb', true)->andReturn([]);
        $locator->shouldReceive('listResources')->once()->with('locale://ff_FF', true)->andReturn([]);
        $locator->shouldReceive('listResources')->once()->with('locale://ee_EE', true)->andReturn([]);

        // Prepare mock FileLoader - No files, so loader shouldn't load anything
        $fileLoader = Mockery::mock(ArrayFileLoader::class);
        $fileLoader->shouldNotReceive('setPaths');
        $fileLoader->shouldNotReceive('load');

        // Get dictionary
        $dictionary = new Dictionary($locale, $locator, $fileLoader);
        $data = $dictionary->getDictionary();

        // Perform assertions
        $this->assertIsArray($data);
        $this->assertEquals([], $data);
    }

    /**
     * @depends testGetDictionary_withManyDependentLocale
     */
    public function testGetDictionary_withRecursiveDependentLocale(): void
    {
        // Set expectations
        $aa_AA_FILE = [
            'Foo'  => 'Bar',
            'test' => [
                'aaa' => 'AAA',
                'ccc' => '', // Overwrites "CCC"
                'ddd' => 'DDD', // Overwrites ""
            ],
        ];

        // Prepare dependent mocked locale - ff_FF && aa_bb
        $localeDependent = Mockery::mock(Locale::class);
        $locale = Mockery::mock(Locale::class);

        $localeDependent->shouldReceive('getDependentLocales')->andReturn([$locale]);
        $localeDependent->shouldReceive('getDependentLocalesIdentifier')->andReturn(['aa_bb']);
        $localeDependent->shouldReceive('getIdentifier')->andReturn('ff_FF');

        $locale->shouldReceive('getDependentLocales')->andReturn([$localeDependent]);
        $locale->shouldReceive('getDependentLocalesIdentifier')->andReturn(['ff_FF']);
        $locale->shouldReceive('getIdentifier')->andReturn('aa_bb');

        // Prepare first mock Resource - File `Foo/Bar/File1.php`
        $file1 = Mockery::mock(Resource::class);
        $file1->shouldReceive('getExtension')->andReturn('php');
        $file1->shouldReceive('__toString')->andReturn('Foo/Bar/File1.php');

        // Prepare mock Locator - Return no file on ff_FF
        $locator = Mockery::mock(ResourceLocator::class);
        $locator->shouldReceive('listResources')->once()->with('locale://aa_bb', true)->andReturn([$file1]);
        $locator->shouldReceive('listResources')->never()->with('locale://ff_FF', true);

        // Prepare mock FileLoader - No files, so loader shouldn't load anything
        $fileLoader = Mockery::mock(ArrayFileLoader::class);
        $fileLoader->shouldReceive('setPaths')->once()->with(['Foo/Bar/File1.php']);
        $fileLoader->shouldReceive('load')->once()->andReturn($aa_AA_FILE);

        // Get dictionary
        $dictionary = new Dictionary($locale, $locator, $fileLoader);

        // Expect exception
        $this->expectException(\LogicException::class);
        $data = $dictionary->getDictionary();
    }

    /**
     * Integration test with default.
     *
     * @ depends testConstructor
     */
    public function testGetDictionary_withRealLocale(): void
    {
        $locale = new Locale('es_ES');
        $dictionary = new Dictionary($locale, $this->locator);

        $expectedResult = [
            'FOO' => 'BAR',  // bar/bar.php file will be loaded first
            'CAR' => 'Coche',
            'BAR' => 'Bar', // ...but zzz/bar.php will be loaded LAST because of alphabetical order !
        ];

        $data = $dictionary->getDictionary();

        $this->assertIsArray($data);
        $this->assertEquals($expectedResult, $data);
    }

    /**
     * @ depends testConstructor
     */
    public function testGetDictionary_withRealLocale_withDependentLocaleDataOnBoth(): void
    {
        // Set expectations
        // fr_FR depends on en_US. So FR data will be loaded over EN data
        // Replicate testGetDictionary_withDependentLocaleDataOnBoth result
        $expectedResult = [
            'Foo'  => 'Bar',
            'test' => [
                'aaa'          => 'AAA',
                'ccc'          => '',
                'ddd'          => 'DDD',
                'bbb'          => 'BBB',
                '@TRANSLATION' => 'Test',
            ],
            'Bar'  => 'Foo',
        ];

        // Get dictionary
        $locale = new Locale('fr_FR');
        $dictionary = new Dictionary($locale, $this->locator);
        $data = $dictionary->getDictionary();

        // Perform assertions
        $this->assertIsArray($data);
        $this->assertEquals($expectedResult, $data);
    }

    /**
     * @ depends testConstructor
     */
    public function testGetDictionary_forFlat(): void
    {
        // Set expectations
        // fr_FR depends on en_US. So FR data will be loaded over EN data
        // Replicate testGetDictionary_withDependentLocaleDataOnBoth result
        $expectedResult = [
            'Foo'               => 'Bar',
            'test.@TRANSLATION' => 'Test',
            'test.aaa'          => 'AAA',
            'test.ccc'          => '',
            'test.ddd'          => 'DDD',
            'test.bbb'          => 'BBB',
            'Bar'               => 'Foo',
        ];

        // Get dictionary
        $locale = new Locale('fr_FR');
        $dictionary = new Dictionary($locale, $this->locator);
        $data = $dictionary->getFlattenDictionary();

        // Perform assertions
        $this->assertIsArray($data);
        $this->assertEquals($expectedResult, $data);
    }

    public function testGetDictionary_withRealLocale_withThirdDependentLocale(): void
    {
        // Set expectations
        // fr_CA depends on fr_FR which depends on en_US.
        // So CA data will be loaded over FR data will be loaded over EN data
        // 'foo' key will be different
        $expectedResult = [
            'Foo'  => 'Tabarnak',
            'test' => [
                'aaa'          => 'AAA',
                'ccc'          => '',
                'ddd'          => 'DDD',
                'bbb'          => 'BBB',
                '@TRANSLATION' => 'Test',
            ],
            'Bar'  => 'Foo',
        ];

        // Get dictionary
        $locale = new Locale('fr_CA');
        $dictionary = new Dictionary($locale, $this->locator);
        $data = $dictionary->getDictionary();

        // Perform assertions
        $this->assertIsArray($data);
        $this->assertEquals($expectedResult, $data);
    }
}
