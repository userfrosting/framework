<?php

/*
 * UserFrosting Support (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/support
 * @copyright Copyright (c) 2013-2019 Alexander Weissman
 * @license   https://github.com/userfrosting/support/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Support\Tests\Util;

use PHPUnit\Framework\TestCase;
use UserFrosting\Support\Util\Util;

class UtilTest extends TestCase
{
    public function testStringMatchesSuccess()
    {
        $str = 'assets-raw/admin/assets/local/widgets/js/users.js';

        $patterns = [
            '^assets-raw',
            '^assets-raw/(.*)',
            '^api/owls',
            '^test/assets-raw',
        ];

        $matches = [];
        $this->assertTrue(Util::stringMatches($patterns, $str, $matches));

        $this->assertEquals([
            '^assets-raw' => [
                'assets-raw',
            ],
            '^assets-raw/(.*)' => [
                'assets-raw/admin/assets/local/widgets/js/users.js',
                'admin/assets/local/widgets/js/users.js',
            ],
        ], $matches);
    }

    public function testStringMatchesFail()
    {
        $str = 'admin/owls/voles';

        $patterns = [
            '^assets-raw',
            '^owls',
            '^api/owls',
        ];

        $this->assertFalse(Util::stringMatches($patterns, $str));
    }

    /**
     * @param string $prefix
     * @param string $expectedResult
     *
     * @testWith ["", "owls::voles"]
     *           ["::", "owls::voles"]
     *           ["owls", "::voles"]
     *           ["owls::", "voles"]
     *           ["owls::voles", ""]
     */
    public function testStripPrefix($prefix, $expectedResult)
    {
        $str = 'owls::voles';
        $this->assertSame($expectedResult, Util::stripPrefix($str, $prefix));
    }

    /**
     * @param string $uri
     * @param string $expectedResult
     *
     * @dataProvider normalizePathDataProvider
     */
    public function testNormalizePath($uri, $expectedResult)
    {
        $this->assertSame($expectedResult, Util::normalizePath($uri, false, false));
    }

    public function normalizePathDataProvider()
    {
        return [
            ['', ''],
            ['./', ''],
            ['././/./', ''],
            ['././/../', false],
            ['/', '/'],
            ['//', '/'],
            ['///', '/'],
            ['/././', '/'],
            ['foo', 'foo'],
            ['/foo', '/foo'],
            ['//foo', '/foo'],
            ['/foo/', '/foo/'],
            ['//foo//', '/foo/'],
            ['path/to/file.txt', 'path/to/file.txt'],
            ['path/to/../file.txt', 'path/file.txt'],
            ['path/to/../../file.txt', 'file.txt'],
            ['path/to/../../../file.txt', false],
            ['/path/to/file.txt', '/path/to/file.txt'],
            ['/path/to/../file.txt', '/path/file.txt'],
            ['/path/to/../../file.txt', '/file.txt'],
            ['/path/to/../../../file.txt', false],
            ['c:\\', 'c:/'],
            ['c:\\path\\to\file.txt', 'c:/path/to/file.txt'],
            ['c:\\path\\to\../file.txt', 'c:/path/file.txt'],
            ['c:\\path\\to\../../file.txt', 'c:/file.txt'],
            ['c:\\path\\to\../../../file.txt', false],
            ['stream://path/to/file.txt', 'stream://path/to/file.txt'],
            ['stream://path/to/../file.txt', 'stream://path/file.txt'],
            ['stream://path/to/../../file.txt', 'stream://file.txt'],
            ['stream://path/to/../../../file.txt', false],
            [['foo'], false],
        ];
    }

    /**
     * @depends testNormalizePath
     */
    public function testNormalizePathWithThrowExceptionOnNonStringUri()
    {
        $this->expectException(\BadMethodCallException::class);
        Util::normalizePath(['foo'], true, false);
    }

    /**
     * @depends testNormalizePath
     */
    public function testNormalizePathWithThrowExceptionOnBadParts()
    {
        $this->expectException(\BadMethodCallException::class);
        Util::normalizePath('././/../', true, false);
    }

    /**
     * @depends testNormalizePath
     */
    public function testNormalizePathWithSplitStream()
    {
        $this->assertSame(['stream', 'path/to/file.txt'], Util::normalizePath('stream://path/to/file.txt', false, true));
    }
}
