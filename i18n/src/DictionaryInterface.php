<?php

/*
 * UserFrosting i18n (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/i18n
 * @copyright Copyright (c) 2013-2019 Alexander Weissman, Louis Charette
 * @license   https://github.com/userfrosting/i18n/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\I18n;

use Illuminate\Contracts\Config\Repository;

/**
 * Locale Dictionary.
 *
 * Used to return all "Key => translation" data matrix
 * Extend the Config repository to have acess to all the standard `has`, `get`,
 * etc. public methods on the dictionnay array
 *
 * @author Louis Charette
 */
interface DictionaryInterface extends Repository
{
    /**
     * Returns all loaded locale Key => Translation data dictionary.
     *
     * @return (string|array)[] The locale dictionary
     */
    public function getDictionary(): array;

    /**
     * Return the associate locale.
     *
     * @return LocaleInterface
     */
    public function getLocale(): LocaleInterface;

    /**
     * Return the dictionnary as a flatten array, using dot notation.
     *
     * @return string[]
     */
    public function getFlattenDictionary(): array;
}
