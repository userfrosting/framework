<?php

/*
 * UserFrosting Framework (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/framework
 * @copyright Copyright (c) 2013-2021 Alexander Weissman, Louis Charette, Jordan Mele
 * @license   https://github.com/userfrosting/framework/blob/master/LICENSE.md (MIT License)
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
