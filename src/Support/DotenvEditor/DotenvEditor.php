<?php

/*
 * UserFrosting Support (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/support
 * @copyright Copyright (c) 2013-2019 Alexander Weissman
 * @license   https://github.com/userfrosting/support/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Support\DotenvEditor;

use Jackiedo\DotenvEditor\DotenvEditor as Editor;
use Jackiedo\DotenvEditor\DotenvFormatter;
use Jackiedo\DotenvEditor\DotenvReader;
use Jackiedo\DotenvEditor\DotenvWriter;
use Jackiedo\DotenvEditor\Exceptions\FileNotFoundException;

/**
 * DotenvEditor.
 *
 * Implementation of Jackiedo DotenvEditor for use in UserFrosting
 *
 * @author Louis Charette
 */
class DotenvEditor extends Editor
{
    /**
     * Create a new DotenvEditor instance.
     *
     * @param string $backupPath
     * @param bool   $autoBackup
     *
     * @throws FileNotFoundException
     */
    public function __construct($backupPath = '', $autoBackup = true)
    {
        $this->formatter = new DotenvFormatter();
        $this->reader = new DotenvReader($this->formatter);
        $this->writer = new DotenvWriter($this->formatter);

        if (!is_dir($backupPath)) {
            throw new FileNotFoundException("Backup path `$backupPath` is not a directory");
        }

        $this->backupPath = $backupPath;
        $this->autoBackup = $autoBackup;
    }

    /**
     * Load file for working.
     *
     * @param string      $filePath          The file path
     * @param bool        $restoreIfNotFound Restore this file from other file if it's not found
     * @param string|null $restorePath       The file path you want to restore from
     *
     * @return DotenvEditor
     */
    public function load($filePath = null, $restoreIfNotFound = false, $restorePath = null)
    {
        //Fail if path is null to maintain compatibility with Jackiedo\DotenvEditor
        if (is_null($filePath)) {
            throw new \InvalidArgumentException('File path cannot be null');
        }

        $this->resetContent();
        $this->filePath = $filePath;

        $this->reader->load($this->filePath);

        if (file_exists($this->filePath)) {
            $this->writer->setBuffer($this->getContent());

            return $this;
        } elseif ($restoreIfNotFound) {
            $this->restore($restorePath);

            return $this;
        } else {
            return $this;
        }
    }
}
