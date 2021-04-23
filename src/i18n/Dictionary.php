<?php

/*
 * UserFrosting i18n (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/i18n
 * @copyright Copyright (c) 2013-2019 Alexander Weissman, Louis Charette
 * @license   https://github.com/userfrosting/i18n/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\I18n;

use Illuminate\Support\Arr;
use UserFrosting\Support\Repository\Loader\ArrayFileLoader;
use UserFrosting\Support\Repository\Loader\FileRepositoryLoader;
use UserFrosting\Support\Repository\Repository;
use UserFrosting\UniformResourceLocator\ResourceLocatorInterface;

/**
 * Locale Dictionary.
 *
 * Load all locale all "Key => translation" data matrix
 *
 * @author Louis Charette
 */
class Dictionary extends Repository implements DictionaryInterface
{
    /**
     * @var string Base URI for locator
     */
    protected $uri = 'locale://';

    /**
     * @var LocaleInterface
     */
    protected $locale;

    /**
     * @var ResourceLocatorInterface
     */
    protected $locator;

    /**
     * @var FileRepositoryLoader
     */
    protected $fileLoader;

    /**
     * @var (string|array)[] Locale "Key => translation" data matrix cache
     */
    protected $items = [];

    /**
     * @param LocaleInterface          $locale
     * @param ResourceLocatorInterface $locator
     * @param FileRepositoryLoader     $fileLoader File loader used to load each dictionnay files (default to Array Loader)
     */
    public function __construct(LocaleInterface $locale, ResourceLocatorInterface $locator, FileRepositoryLoader $fileLoader = null)
    {
        $this->locale = $locale;
        $this->locator = $locator;
        $this->fileLoader = is_null($fileLoader) ? new ArrayFileLoader([]) : $fileLoader;
    }

    /**
     * {@inheritdoc}
     */
    public function getDictionary(): array
    {
        if (empty($this->items)) {
            $this->items = $this->loadDictionary();
        }

        return $this->items;
    }

    /**
     * {@inheritdoc}
     */
    public function getFlattenDictionary(): array
    {
        return Arr::dot($this->getDictionary());
    }

    /**
     * Set the locator base URI (default 'locale://').
     *
     * @param string $uri
     */
    public function setUri(string $uri): void
    {
        $this->uri = $uri;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocale(): LocaleInterface
    {
        return $this->locale;
    }

    /**
     * Return the file repository loader used to load.
     *
     * @return FileRepositoryLoader
     */
    public function getFileLoader(): FileRepositoryLoader
    {
        return $this->fileLoader;
    }

    /**
     * Load the dictionary from file.
     *
     * @return (string|array)[] The locale dictionary
     */
    protected function loadDictionary(): array
    {
        $dictionary = [];

        // List of loaded locales
        $loadedLocale = [$this->locale->getIdentifier()];

        // Get list of files to load
        $files = $this->getFiles();
        $files = $this->filterDictionaryFiles($files);

        // Load all files content if files are present
        if (!empty($files)) {
            $loader = $this->getFileLoader();
            $loader->setPaths($files);

            $dictionary = $loader->load();
        }

        // Now load dependent dictionnaries
        foreach ($this->locale->getDependentLocales() as $locale) {

            // Stop if locale already loaded to prevent recursion
            $localesToLoad = array_merge([$locale->getIdentifier()], $locale->getDependentLocalesIdentifier());
            $intersection = array_intersect($localesToLoad, $loadedLocale);
            if (!empty($intersection)) {
                throw new \LogicException("Can't load dictionary. Dependencies recursion detected : ".implode(', ', $intersection));
            }

            $dependentDictionary = new self($locale, $this->locator, $this->fileLoader);
            $dictionary = array_replace_recursive($dependentDictionary->getDictionary(), $dictionary);

            $loadedLocale[] = $locale->getIdentifier();
        }

        return $dictionary;
    }

    /**
     * Remove config files from locator results and convert ResourceInterface to path/string.
     *
     * @param \UserFrosting\UniformResourceLocator\ResourceInterface[] $files
     *
     * @return string[]
     */
    protected function filterDictionaryFiles(array $files): array
    {
        return array_filter($files, function ($file) {
            if ($file->getExtension() == 'php') {
                return (string) $file;
            }
        });
    }

    /**
     * List all files for a given locale using the locator.
     *
     * @return \UserFrosting\UniformResourceLocator\ResourceInterface[]
     */
    protected function getFiles(): array
    {
        $ressources = $this->locator->listResources($this->uri.$this->locale->getIdentifier(), true);
        $ressources = array_reverse($ressources);

        return $ressources;
    }
}
