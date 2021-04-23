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

/**
 * Helper class to compare two dictionaries.
 * Can be used to determine which keys/values are missing from a dictionary (right) compared to a base directory (left).
 *
 * @author Louis Charette
 */
class Compare
{
    /**
     * Compares keys and values from left dictionary against right dictionary and returns the difference.
     *
     * @param DictionaryInterface $leftDictionary
     * @param DictionaryInterface $rightDictionary
     * @param bool                $undot           If true, return the dot notation associative array. If false, returns the normal multidimensional associative array [Default: true]
     *
     * @return string[]
     */
    public static function dictionaries(DictionaryInterface $leftDictionary, DictionaryInterface $rightDictionary, bool $undot = false): array
    {
        $diff = array_diff_assoc($leftDictionary->getFlattenDictionary(), $rightDictionary->getFlattenDictionary());

        if ($undot) {
            return self::undot($diff);
        }

        return $diff;
    }

    /**
     * Compares keys from left dictionary against right dictionary.
     * Returns a list of keys present in the left dictory, but not found in the right one.
     * Can be used to sync both dictionary content.
     *
     * @param DictionaryInterface $leftDictionary
     * @param DictionaryInterface $rightDictionary
     *
     * @return string[] List of keys
     */
    public static function dictionariesKeys(DictionaryInterface $leftDictionary, DictionaryInterface $rightDictionary): array
    {
        $diff = array_diff_key($leftDictionary->getFlattenDictionary(), $rightDictionary->getFlattenDictionary());

        return array_keys($diff);
    }

    /**
     * Compares values from left dictionary against right dictionary to find same values.
     * Returns a list of values which are the same in both dictionaries.
     * Can be used to find all values in the right directory that might not have been translated compared to the left one.
     * For example, when comparing french and english dictionaries, this can be used to return all English values present in the French dictionary.
     *
     * @param DictionaryInterface $leftDictionary
     * @param DictionaryInterface $rightDictionary
     * @param bool                $undot           If true, return the dot notation associative array. If false, returns the normal multidimensional associative array [Default: false]
     *
     * @return string[]
     */
    public static function dictionariesValues(DictionaryInterface $leftDictionary, DictionaryInterface $rightDictionary, bool $undot = false): array
    {
        $diff = array_intersect_assoc($leftDictionary->getFlattenDictionary(), $rightDictionary->getFlattenDictionary());

        if ($undot) {
            return self::undot($diff);
        }

        return $diff;
    }

    /**
     * Returns all keys for which the value is empty.
     * Can be used to find all keys that needs to be translated in the dictionary.
     *
     * @param DictionaryInterface $dictionary
     *
     * @return string[] List of keys
     */
    public static function dictionariesEmptyValues(DictionaryInterface $dictionary): array
    {
        $diff = array_filter($dictionary->getFlattenDictionary(), function ($value) {
            return $value == '';
        });

        return array_keys($diff);
    }

    /**
     * Transfer dot notation associative array back into multidimentional associative array.
     *
     * @param mixed[] $array
     *
     * @return mixed[]
     */
    protected static function undot(array $array): array
    {
        $result = [];
        foreach ($array as $key => $value) {
            Arr::set($result, $key, $value);
        }

        return $result;
    }
}
