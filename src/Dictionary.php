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
use UserFrosting\UniformResourceLocator\ResourceLocatorInterface;

/**
 * Locale Dictionary.
 *
 * Load all locale all "Key => translation" data matrix
 *
 * @author Louis Charette
 */
class Dictionary implements DictionaryInterface
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
     * @var array Locale "Key => translation" data matrix cache
     */
    protected $dictionary = [];

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
     * Returns all loaded locale Key => Translation data dictionary.
     * Won't load the whole thing twice if already loaded in the class.
     *
     * @return string[] The locale dictionary
     */
    public function getDictionary(): array
    {
        if (empty($this->dictionary)) {
            $this->dictionary = $this->loadDictionary();
        }

        return $this->dictionary;
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
     * Return the associate locale.
     *
     * @return LocaleInterface
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
        $loadedLocale = [$this->locale->getIndentifier()];

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
        // TODO : Split this in a sub method
        foreach ($this->locale->getDependentLocales() as $locale) {

            // Stop if locale already loaded to prevent recursion
            $localesToLoad = array_merge([$locale->getIndentifier()], $locale->getDependentLocalesIdentifier());
            $intersection = array_intersect($localesToLoad, $loadedLocale);
            if (!empty($intersection)) {
                throw new \LogicException("Can't load dictionary. Dependencies recursion detected : ".implode(', ', $intersection));
            }

            $dependentDictionary = new self($locale, $this->locator, $this->fileLoader);
            $dictionary = array_replace_recursive($dependentDictionary->getDictionary(), $dictionary);

            $loadedLocale[] = $locale->getIndentifier();
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
        return $this->locator->listResources($this->uri.$this->locale->getIndentifier(), true);
    }

    /**
     * Determine if the given configuration value exists.
     *
     * @param  string  $key
     * @return bool
     */
    public function has($key)
    {
        return Arr::has($this->all(), $key);
    }

    /**
     * Get the specified configuration value.
     *
     * @param  array|string  $key
     * @param  mixed   $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        if (is_array($key)) {
            return $this->getMany($key);
        }

        return Arr::get($this->all(), $key, $default);
    }

    /**
     * Get many configuration values.
     *
     * @param  array  $keys
     * @return array
     */
    public function getMany($keys)
    {
        $config = [];

        foreach ($keys as $key => $default) {
            if (is_numeric($key)) {
                [$key, $default] = [$default, null];
            }

            $config[$key] = Arr::get($this->all(), $key, $default);
        }

        return $config;
    }

    /**
     * Set a given configuration value.
     *
     * @param  array|string  $key
     * @param  mixed   $value
     * @return void
     */
    public function set($key, $value = null)
    {
        $keys = is_array($key) ? $key : [$key => $value];
        $items = $this->all();

        foreach ($keys as $key => $value) {
            Arr::set($items, $key, $value);
        }
    }

    /**
     * Prepend a value onto an array configuration value.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function prepend($key, $value)
    {
        $array = $this->get($key);

        array_unshift($array, $value);

        $this->set($key, $array);
    }

    /**
     * Push a value onto an array configuration value.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function push($key, $value)
    {
        $array = $this->get($key);

        $array[] = $value;

        $this->set($key, $array);
    }

    /**
     * Get all of the configuration items for the application.
     *
     * @return array
     */
    public function all()
    {
        return $this->getDictionary();
    }

    /**
     * Determine if the given configuration option exists.
     *
     * @param  string  $key
     * @return bool
     */
    public function offsetExists($key)
    {
        return $this->has($key);
    }

    /**
     * Get a configuration option.
     *
     * @param  string  $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->get($key);
    }

    /**
     * Set a configuration option.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($key, $value)
    {
        $this->set($key, $value);
    }

    /**
     * Unset a configuration option.
     *
     * @param  string  $key
     * @return void
     */
    public function offsetUnset($key)
    {
        $this->set($key, null);
    }
}
