<?php

/*
 * UserFrosting Framework (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/framework
 * @copyright Copyright (c) 2013-2021 Alexander Weissman, Louis Charette, Jordan Mele
 * @license   https://github.com/userfrosting/framework/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Support\DotenvEditor;

use InvalidArgumentException;
use Jackiedo\DotenvEditor\DotenvEditor as Editor;
use Jackiedo\DotenvEditor\DotenvFormatter;
use Jackiedo\DotenvEditor\DotenvReader;
use Jackiedo\DotenvEditor\DotenvWriter;

/**
 * Implementation of Jackiedo DotenvEditor for use in UserFrosting.
 */
class DotenvEditor extends Editor
{
    /**
     * Create a new DotenvEditor instance.
     *
     * @param string $backupPath
     * @param bool   $autoBackup
     *
     * @phpstan-ignore-next-line We don't want to use the constructor of the parent class.
     */
    public function __construct(string $backupPath = '', bool $autoBackup = true)
    {
        $this->formatter = new DotenvFormatter();
        $this->reader = new DotenvReader($this->formatter);
        $this->writer = new DotenvWriter($this->formatter);
        $this->backupPath = $backupPath;
        $this->autoBackup = $autoBackup;
    }

    /**
     * Load file for working.
     *
     * @param string|null $filePath          The file path
     * @param bool        $restoreIfNotFound Restore this file from other file if it's not found
     * @param string|null $restorePath       The file path you want to restore from
     *
     * @return DotenvEditor
     */
    public function load($filePath = null, $restoreIfNotFound = false, $restorePath = null)
    {
        //Fail if path is null to maintain compatibility with Jackiedo\DotenvEditor
        if (is_null($filePath)) {
            throw new InvalidArgumentException('File path cannot be null');
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

    /**
     * Set the backup path.
     *
     * @param string $backupPath
     *
     * @return static
     */
    public function setBackupPath(string $backupPath): static
    {
        $this->backupPath = $backupPath;

        return $this;
    }
}
