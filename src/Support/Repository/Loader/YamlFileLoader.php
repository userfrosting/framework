<?php

/*
 * UserFrosting Support (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/support
 * @copyright Copyright (c) 2013-2019 Alexander Weissman
 * @license   https://github.com/userfrosting/support/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Support\Repository\Loader;

use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;
use UserFrosting\Support\Exception\FileNotFoundException;
use UserFrosting\Support\Exception\JsonException;

/**
 * Load content from yaml/json files.
 *
 * @author Alexander Weissman (https://alexanderweissman.com)
 */
class YamlFileLoader extends FileRepositoryLoader
{
    /**
     * {@inheritdoc}
     */
    protected function parseFile(string $path): array
    {
        $doc = $this->fileGetContents($path);
        if ($doc === false) {
            throw new FileNotFoundException("The file '$path' could not be read.");
        }

        try {
            $result = Yaml::parse($doc);
        } catch (ParseException $e) {
            // Fallback to try and parse as JSON, if it fails to be parsed as YAML
            $result = json_decode($doc, true);
            if ($result === null) {
                throw new JsonException("The file '$path' does not contain a valid YAML or JSON document.  JSON error: ".json_last_error());
            }
        }

        // In case `Yaml::parse` returns empty data/file
        if (is_null($result)) {
            return [];
        }

        return $result;
    }

    /**
     * Reads entire path into a string.
     *
     * @param string $path
     *
     * @return string|false
     */
    protected function fileGetContents(string $path)
    {
        return file_get_contents($path);
    }
}
