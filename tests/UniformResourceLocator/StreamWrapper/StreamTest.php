<?php

declare(strict_types=1);

/*
 * UserFrosting Framework (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/framework
 * @copyright Copyright (c) 2013-2021 Alexander Weissman, Louis Charette, Jordan Mele
 * @license   https://github.com/userfrosting/framework/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Tests\UniformResourceLocator\StreamWrapper;

use PHPUnit\Framework\TestCase;
use UserFrosting\UniformResourceLocator\ResourceLocator;
use UserFrosting\UniformResourceLocator\StreamWrapper\Stream;
use UserFrosting\UniformResourceLocator\StreamWrapper\StreamBuilder;

/**
 * Tests for Stream.
 */
class StreamTest extends TestCase
{
    protected string $stream;
    protected string $file;
    protected string $bad_file;
    protected string $bad_dir;
    protected StreamBuilder $builder;
    protected ResourceLocator $locator;

    public function setUp(): void
    {
        parent::setUp();

        // Remove streams that would have been registered by another test
        @stream_wrapper_unregister('foo');
        @stream_wrapper_unregister('bar');

        // Setup test strings
        $this->stream = 'foo://';
        $this->file = $this->stream . 'test.txt';
        $this->bad_file = $this->stream . 'bad.txt';
        $this->bad_dir = $this->stream . 'bar/foo';

        // Setup builder. 'bar' stream will point to nothing
        // 'foo' stream will be setup by locator
        $this->builder = new StreamBuilder();
        $this->builder->add('bar', Stream::class);

        // Setup test locator
        $this->locator = new ResourceLocator(__DIR__ . '/data', streamBuilder: $this->builder);
        $this->locator->registerSharedStream('foo', '');
    }

    public function tearDown(): void
    {
        parent::tearDown();

        // Reset dir using normal method
        @unlink(__DIR__ . '/data/test.txt');
        @rmdir(__DIR__ . '/data/bar');
        @rmdir(__DIR__ . '/data/foo');

        // Call stream_wrapper_unregister for later tests
        $this->builder->remove('foo');
        $this->builder->remove('bar');
    }

    public function testSimpleFile(): void
    {
        // Test file creation
        $this->assertFileDoesNotExist(__DIR__ . '/data/test.txt');
        $this->assertFalse(file_exists($this->file));
        $this->assertTrue(touch($this->file, time(), time()));
        $this->assertTrue(touch($this->file));
        $this->assertFileExists(__DIR__ . '/data/test.txt');
        $this->assertTrue(file_exists($this->file));
        $this->assertSame('', file_get_contents($this->file));
        $this->assertSame(3, file_put_contents($this->file, 'bar')); //@phpstan-ignore-line
        $this->assertSame('bar', file_get_contents($this->file));
        $this->assertSame(3, fwrite(fopen($this->file, 'r+'), 'foo')); // @phpstan-ignore-line
        $this->assertSame('foo', file_get_contents($this->file));
        $this->assertTrue(unlink($this->file));
        $this->assertFileDoesNotExist(__DIR__ . '/data/test.txt');
    }

    /**
     * Test dir creation & manipulation
     */
    public function testSimpleDir(): void
    {
        // With existing dir
        $this->assertTrue(file_exists($this->stream));
        $this->assertTrue(is_dir($this->stream));

        // With new dir
        $this->assertFalse(file_exists($this->stream . 'bar'));
        $this->assertFalse(is_dir($this->stream . 'bar'));
        $this->assertTrue(mkdir($this->stream . 'bar', recursive: true));
        $this->assertTrue(file_exists($this->stream . 'bar'));
        $this->assertTrue(is_dir($this->stream . 'bar'));

        $dir = opendir($this->stream);
        $this->assertIsResource($dir);
        $entries = [];
        while ($entry = readdir($dir)) {
            $entries[] = $entry;
        }
        $this->assertContains('bar', $entries);

        $this->assertTrue(rename($this->stream . 'bar', $this->stream . 'foo'));
        $this->assertTrue(rmdir($this->stream . 'foo'));
    }

    /**
     * @depends testSimpleFile
     */
    public function testDirectoryRewind(): void
    {
        $this->assertTrue(touch($this->file)); // Touch basic file
        $directoryResource = opendir($this->stream);
        $this->assertIsResource($directoryResource);
        $entry = readdir($directoryResource);
        rewinddir($directoryResource);
        $this->assertSame($entry, readdir($directoryResource));
        $this->assertTrue(unlink($this->file)); // Reset state
    }

    /**
     * @depends testSimpleFile
     */
    public function testChmod(): void
    {
        touch($this->file); // Touch basic file
        $this->assertTrue(is_readable($this->file));

        $stat = stat($this->file);
        $pwuid = posix_getpwuid($stat['uid']); // @phpstan-ignore-line

        // chmod
        $permission = fileperms($this->file);
        $this->assertIsInt($permission);
        $this->assertTrue(chmod($this->file, 0755));

        // chown
        $owner = fileowner($this->file);
        $this->assertIsInt($owner);
        $this->assertTrue(chown($this->file, $owner));
        $this->assertTrue(chown($this->file, $pwuid['name'])); // @phpstan-ignore-line

        // chgrp
        $group = filegroup($this->file);
        $this->assertIsInt($group);
        $this->assertTrue(chgrp($this->file, $group));

        unlink($this->file);  // Reset state
    }

    /**
     * @depends testSimpleFile
     */
    public function testFOpen(): void
    {
        // stream_open will trigger an error even if the stream return false.
        set_error_handler(function ($no, $str, $file, $line) { // @phpstan-ignore-line
        });

        touch($this->file); // Touch basic file
        $this->assertFalse(fopen($this->file, 'x'));
        unlink($this->file);  // Reset state
    }

    /**
     * @depends testSimpleFile
     */
    public function testFSeek(): void
    {
        // fseek returns 0 on success and -1 on a failure.
        touch($this->file); // Touch basic file
        $this->assertSame(0, fseek(fopen($this->file, 'r'), 0)); // @phpstan-ignore-line
        unlink($this->file); // Reset state
    }

    public function testFLock(): void
    {
        touch($this->file); // Touch basic file
        $fp = fopen($this->file, 'r+');
        $this->assertTrue(flock($fp, LOCK_EX)); // @phpstan-ignore-line
        unlink($this->file); // Reset state
    }

    /**
     * Test when a stream exist, but the resource doesn't
     *
     * @depends testSimpleFile
     */
    public function testLocatorResourceIsFalse(): void
    {
        // Test making dir on non-existing stream
        // Test getPath return the proper false for both options.
        $this->assertFalse(mkdir('bar://'));
        $this->assertFalse(mkdir('bar://foo'));

        // Test touch will fail when resource stream doesn't exist.
        $this->assertFalse(touch('bar://'));
    }

    /**
     * @depends testSimpleFile
     */
    public function testOpenDirOnFile(): void
    {
        // dir_opendir will trigger an error even if the stream return false.
        set_error_handler(function ($no, $str, $file, $line) { // @phpstan-ignore-line
        });

        touch($this->file); // Touch basic file
        $this->assertFalse(opendir($this->file));
        unlink($this->file);  // Reset state
    }

    public function testDoesNotExist(): void
    {
        $this->assertFalse(file_exists($this->bad_file));
        $this->assertFalse(file_exists($this->bad_dir));
        $this->assertFalse(chmod($this->bad_file, 0755));
        $this->assertFalse(unlink($this->bad_file));
        $this->assertFalse(is_dir($this->bad_dir));
        $this->assertFalse(rename($this->bad_file, $this->bad_file));
        $this->assertFalse(rmdir($this->bad_dir));
    }

    public function testDoesNotExistWithException(): void
    {
        // stream_open will trigger an error even if the stream return false.
        set_error_handler(function ($no, $str, $file, $line) { // @phpstan-ignore-line
        });

        $this->assertFalse(opendir($this->bad_dir));
        $this->assertFalse(file_get_contents($this->bad_file));
        $this->assertFalse(mkdir($this->bad_dir));
    }

    public function testChgrpWithString(): void
    {
        // stream_open will trigger an error even if the stream return false.
        set_error_handler(function ($no, $str, $file, $line) { // @phpstan-ignore-line
        });

        touch($this->file); // Touch basic file
        $this->assertFalse(chgrp($this->file, 'root'));
        unlink($this->file);  // Reset state
    }
}
