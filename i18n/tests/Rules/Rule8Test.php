<?php

/*
 * UserFrosting i18n (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/i18n
 * @copyright Copyright (c) 2013-2019 Alexander Weissman, Louis Charette
 * @license   https://github.com/userfrosting/i18n/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\I18n\Tests\Rules;

class Rule8Test extends RuleBase
{
    protected $ruleToTest = "\UserFrosting\I18n\PluralRules\Rule8";

    /**
     * Families: Slavic (Slovak, Czech)
     * 1 - 1
     * 2 - 2, 3, 4
     * 3 - everything else: 0, 5, 6, 7, ...
     */
    public function ruleProvider()
    {
        return [
            [0, 3],
            [1, 1],
            [2, 2],
            [3, 2],
            [4, 2],
            [5, 3],
            [11, 3],
            [12, 3],
            [13, 3],
            [19, 3],
            [20, 3],
            [21, 3],
            [40, 3],
            [100, 3],
            [101, 3],
            [110, 3],
            [111, 3],
            [128, 3],
        ];
    }
}
