<?php

/*
 * UserFrosting i18n (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/i18n
 * @copyright Copyright (c) 2013-2019 Alexander Weissman, Louis Charette
 * @license   https://github.com/userfrosting/i18n/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\I18n\Tests\Rules;

class Rule11Test extends RuleBase
{
    protected $ruleToTest = "\UserFrosting\I18n\PluralRules\Rule11";

    /**
     * Families: Celtic (Irish Gaeilge)
     * 1 - 1
     * 2 - 2
     * 3 - is 3-6: 3, 4, 5, 6
     * 4 - is 7-10: 7, 8, 9, 10
     * 5 - everything else: 0, 11, 12, ...
     */
    public function ruleProvider()
    {
        return [
            [0, 5],
            [1, 1],
            [2, 2],
            [3, 3],
            [4, 3],
            [5, 3],
            [6, 3],
            [7, 4],
            [8, 4],
            [9, 4],
            [10, 4],
            [11, 5],
            [12, 5],
            [21, 5],
            [100, 5],
            [101, 5],
            [120, 5],
            [121, 5],
            [122, 5],
            [123, 5],
            [124, 5],
            [125, 5],
        ];
    }
}
