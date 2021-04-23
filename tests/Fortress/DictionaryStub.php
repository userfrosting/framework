<?php

/*
 * UserFrosting Framework (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/framework
 * @copyright Copyright (c) 2013-2021 Alexander Weissman, Louis Charette, Jordan Mele
 * @license   https://github.com/userfrosting/framework/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Tests\Fortress;

use UserFrosting\I18n\DictionaryInterface;
use UserFrosting\I18n\LocaleInterface;
use UserFrosting\Support\Repository\Repository;

class DictionaryStub extends Repository implements DictionaryInterface
{
    public function __construct()
    {
    }

    public function getDictionary(): array
    {
        return [];
    }

    public function getLocale(): LocaleInterface
    {
    }

    public function getFlattenDictionary(): array
    {
    }
}
