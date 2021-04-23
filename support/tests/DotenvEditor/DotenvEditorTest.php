<?php

/*
 * UserFrosting Support (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/support
 * @copyright Copyright (c) 2013-2019 Alexander Weissman
 * @license   https://github.com/userfrosting/support/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Support\Tests\DotenvEditor;

use Jackiedo\DotenvEditor\Exceptions\FileNotFoundException;
use PHPUnit\Framework\TestCase;
use UserFrosting\Support\DotenvEditor\DotenvEditor;

class DotenvEditorTest extends TestCase
{
    protected $basePath = __DIR__.'/data/';

    public function testConstructor()
    {
        $editor = new DotenvEditor($this->basePath.'.env-backups/');
        $this->assertInstanceOf(DotenvEditor::class, $editor);

        return $editor;
    }

    /**
     * @depends testConstructor
     */
    public function testBackupException()
    {
        $this->expectException(FileNotFoundException::class);
        new DotenvEditor($this->basePath.'backups/');
    }

    /**
     * @depends testConstructor
     */
    public function testLoad()
    {
        $editor = new DotenvEditor($this->basePath.'.env-backups/');
        $editor->load($this->basePath.'.env');
        $this->assertEquals('dbpass', $editor->getValue('DB_PASSWORD'));
    }

    /**
     * @depends testConstructor
     * @depends testLoad
     */
    public function testBackup()
    {
        $editor = new DotenvEditor($this->basePath.'.env-backups/');
        $editor->load($this->basePath.'.env');

        $backups_before = $editor->getBackups();
        $editor->backup();
        $backups_after = $editor->getBackups();
        $this->assertEquals(1, count($backups_after) - count($backups_before));

        $editor->deleteBackups();
        $this->assertCount(0, $editor->getBackups());

        // Reset our test dir
        touch($this->basePath.'.env-backups/.gitkeep');
    }

    /**
     * @depends testConstructor
     */
    public function testLoadPathNotExist()
    {
        $editor = new DotenvEditor($this->basePath.'.env-backups/');
        $result = $editor->load($this->basePath.'.fakeEnv');
        $this->assertEquals($editor, $result);
    }

    /**
     * @depends testConstructor
     */
    public function testLoadPathIsNull()
    {
        $editor = new DotenvEditor($this->basePath.'.env-backups/');
        $this->expectException(\InvalidArgumentException::class);
        $result = $editor->load();
    }

    /**
     * @depends testConstructor
     */
    public function testLoadPathNotExistAndRestore()
    {
        // Create a backup
        $editor = new DotenvEditor($this->basePath.'.env-backups/');
        $editor->load($this->basePath.'.env');
        $editor->backup();

        $result = $editor->load($this->basePath.'.fakeEnv', true);
        $this->assertEquals($editor, $result);

        // Reset our test dir
        unlink($this->basePath.'.fakeEnv');
        $editor->deleteBackups();
        touch($this->basePath.'.env-backups/.gitkeep');
    }
}
