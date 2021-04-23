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
use UserFrosting\I18n\Compare;
use UserFrosting\I18n\DictionaryInterface;

class CompareTest extends TestCase
{
    /**
     * @var DictionaryInterface
     */
    protected $left;

    /**
     * @var DictionaryInterface
     */
    protected $right;

    /**
     * {@inheritdoc}
     */
    public function setup(): void
    {
        parent::setup();

        $this->left = Mockery::mock(DictionaryInterface::class);
        $this->left->shouldReceive('getFlattenDictionary')->andReturn([
            'Foo'               => 'Bar',
            'test.@TRANSLATION' => 'Test',
            'test.aaa'          => 'AAA',
            'test.bbb'          => 'BBB',
            'test.ccc'          => 'CCC',
            'test.ddd'          => 'DDD',
            'Bar'               => 'Foo',
            'color'             => 'Color',
        ]);

        $this->right = Mockery::mock(DictionaryInterface::class);
        $this->right->shouldReceive('getFlattenDictionary')->andReturn([
            'Foo'                => 'Bar',
            'test.@TRANSLATION'  => 'Test',
            'test.aaa'           => 'AAA',
            'test.ccc'           => '',
            'test.bbb'           => 'BBB',
            'color.@TRANSLATION' => 'Color',
            'color.red'          => 'Red',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function testDictionaries(): void
    {
        // Compare flatten dictionaries
        // L -> R
        $this->assertSame([
            'test.ccc' => 'CCC',
            'test.ddd' => 'DDD',
            'Bar'      => 'Foo',
            'color'    => 'Color',
        ], Compare::dictionaries($this->left, $this->right));

        // R -> L
        $this->assertSame([
            'test.ccc'           => '',
            'color.@TRANSLATION' => 'Color',
            'color.red'          => 'Red',
        ], Compare::dictionaries($this->right, $this->left));

        // Compare direct dictionaries
        // L -> R
        $this->assertSame([
            'test' => [
                'ccc' => 'CCC',
                'ddd' => 'DDD',
            ],
            'Bar'   => 'Foo',
            'color' => 'Color',
        ], Compare::dictionaries($this->left, $this->right, true));

        // R -> L
        $this->assertSame([
            'test'     => [
                'ccc' => '',
            ],
            'color' => [
                '@TRANSLATION' => 'Color',
                'red'          => 'Red',
            ],
        ], Compare::dictionaries($this->right, $this->left, true));
    }

    public function testDictionariesKeys(): void
    {
        // L -> R
        $this->assertSame([
            'test.ddd',
            'Bar',
            'color',
        ], Compare::dictionariesKeys($this->left, $this->right));

        // R -> L
        $this->assertSame([
            'color.@TRANSLATION',
            'color.red',
        ], Compare::dictionariesKeys($this->right, $this->left));
    }

    public function testDictionariesValues(): void
    {
        // Compare flatten dictionaries
        // L -> R
        $this->assertSame([
            'Foo'               => 'Bar',
            'test.@TRANSLATION' => 'Test',
            'test.aaa'          => 'AAA',
            'test.bbb'          => 'BBB',
        ], Compare::dictionariesValues($this->left, $this->right));

        // R -> L
        $this->assertSame([
            'Foo'               => 'Bar',
            'test.@TRANSLATION' => 'Test',
            'test.aaa'          => 'AAA',
            'test.bbb'          => 'BBB',
        ], Compare::dictionariesValues($this->right, $this->left));

        // Compare direct dictionaries
        // L -> R
        $this->assertSame([
            'Foo'               => 'Bar',
            'test'              => [
                '@TRANSLATION' => 'Test',
                'aaa'          => 'AAA',
                'bbb'          => 'BBB',
            ],
        ], Compare::dictionariesValues($this->left, $this->right, true));

        // R -> L
        $this->assertSame([
            'Foo'               => 'Bar',
            'test'              => [
                '@TRANSLATION' => 'Test',
                'aaa'          => 'AAA',
                'bbb'          => 'BBB',
            ],
        ], Compare::dictionariesValues($this->right, $this->left, true));
    }

    public function testDictionariesEmptyValues(): void
    {
        // Left
        $this->assertSame([], Compare::dictionariesEmptyValues($this->left));

        // Right
        $this->assertSame(['test.ccc'], Compare::dictionariesEmptyValues($this->right));
    }
}
