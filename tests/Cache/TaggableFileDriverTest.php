<?php

/*
 * UserFrosting Framework (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/framework
 * @copyright Copyright (c) 2013-2021 Alexander Weissman, Louis Charette, Jordan Mele
 * @license   https://github.com/userfrosting/framework/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Tests\Cache;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use UserFrosting\Cache\Driver\FileTagSet;
use UserFrosting\Cache\Driver\TaggableFileStore;
use UserFrosting\Cache\Driver\TaggedFileCache;

class TaggableFileDriverTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public $file;
    public $path;

    public function setup(): void
    {
        $this->file = new Filesystem();
        $this->path = __DIR__.'/store';
    }

    public function testTagKeyGeneratesPrefixedKey()
    {
        $store = new TaggableFileStore($this->file, $this->path, []);
        $tagSet = new FileTagSet($store, ['foobar']);
        $this->assertEquals('cache_tags~#~foobar', $tagSet->tagKey('foobar'));
    }

    public function testTagKeyGeneratesPrefixedKeywithCustomSeparator()
    {
        $store = new TaggableFileStore($this->file, $this->path, [
            'separator'=> '~|~',
        ]);
        $tagSet = new FileTagSet($store, ['foobar']);
        $this->assertEquals('cache_tags~|~foobar', $tagSet->tagKey('foobar'));
    }

    public function testPathGeneratesCorrectPathfoKeyWithoutSeparator()
    {
        $reflectionMethod = new \ReflectionMethod(TaggableFileStore::class, 'path');

        $store = new TaggableFileStore($this->file, $this->path, []);
        $reflectionMethod->setAccessible(true);
        $path = $reflectionMethod->invoke($store, 'foobar');

        $this->assertTrue(Str::contains($path, $this->path));
        $this->assertTrue(str_replace($this->path, '', $path) === '/88/43/8843d7f92416211de9ebb963ff4ce28125932878');
    }

    public function testPathGeneratesCorrectPathforKeyWithSeparator()
    {
        $reflectionMethod = new \ReflectionMethod(TaggableFileStore::class, 'path');

        $store = new TaggableFileStore($this->file, $this->path, []);
        $reflectionMethod->setAccessible(true);
        $path = $reflectionMethod->invoke($store, 'boofar~#~foobar');

        $this->assertTrue(Str::contains($path, $this->path));
        $this->assertTrue(str_replace($this->path, '', $path) === '/boofar/88/43/8843d7f92416211de9ebb963ff4ce28125932878');
    }

    public function testPathGeneratesCorrectPathforKeyWithCustomSeparator()
    {
        $reflectionMethod = new \ReflectionMethod(TaggableFileStore::class, 'path');

        $store = new TaggableFileStore($this->file, $this->path, ['separator' => '~|~']);
        $reflectionMethod->setAccessible(true);
        $path = $reflectionMethod->invoke($store, 'boofar~|~foobar');

        $this->assertTrue(Str::contains($path, $this->path));
        $this->assertTrue(str_replace($this->path, '', $path) === '/boofar/88/43/8843d7f92416211de9ebb963ff4ce28125932878');
    }

    public function testTagsReturnsTaggedFileCache()
    {
        $store = new TaggableFileStore($this->file, $this->path, []);

        $cache = $store->tags(['abc', 'def']);

        $this->assertInstanceOf(TaggedFileCache::class, $cache);
    }

    public function testFlushOldTagDeletesTagFolders()
    {
        $filesMock = Mockery::mock(new Filesystem());
        $store = new TaggableFileStore($filesMock, '/', []);

        $filesMock->shouldReceive('directories')->with('/')->andReturn([
            'test/foobar',
            'foobar',
            'testfoobar',
            'testfoobartest',
            'test/testfoobartest',
        ]);

        $filesMock->shouldReceive('deleteDirectory')->with('test/foobar')->once();
        $filesMock->shouldReceive('deleteDirectory')->with('foobar')->once();
        $filesMock->shouldReceive('deleteDirectory')->with('testfoobar')->once();
        $filesMock->shouldReceive('deleteDirectory')->with('testfoobartest')->once();
        $filesMock->shouldReceive('deleteDirectory')->with('test/testfoobartest')->once();

        $store->flushOldTag('foobar');
    }

    public function testFlushOldTagDoesNotDeletesOtherFolders()
    {
        $filesMock = Mockery::mock(new Filesystem());
        $store = new TaggableFileStore($filesMock, '/', []);

        $filesMock->shouldReceive('directories')->with('/')->andReturn([
            'test/foobar/foo',
            'foobar/test',
            'test',
        ]);

        $filesMock->shouldNotReceive('deleteDirectory')->with('test/foobar/foo');
        $filesMock->shouldNotReceive('deleteDirectory')->with('foobar/test');
        $filesMock->shouldNotReceive('deleteDirectory')->with('test');

        $store->flushOldTag('foobar');
    }

    public function testItemKeyCallsTaggedItemKey()
    {
        $store = new TaggableFileStore($this->file, $this->path, []);
        $cache = new TaggedFileCache($store, new FileTagSet($store, ['foobar']));

        $mock = Mockery::mock($cache);

        $mock->shouldReceive('taggedItemKey')->with('test');

        $mock->itemKey('test');
    }

    public function testItemKeyReturnsTaggedItemKey()
    {
        $store = new TaggableFileStore($this->file, $this->path, []);
        $cache = new TaggedFileCache($store, new FileTagSet($store, ['foobar']));

        $mock = Mockery::mock($cache);

        $mock->shouldReceive('taggedItemKey')->with('test')->andReturn('boofar');

        $this->assertEquals('boofar', $mock->itemKey('test'));
    }

    public function tearDown(): void
    {
        Mockery::close();
    }
}
