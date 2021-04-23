<?php

/*
 * UserFrosting i18n (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/i18n
 * @copyright Copyright (c) 2013-2019 Alexander Weissman, Louis Charette
 * @license   https://github.com/userfrosting/i18n/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\I18n\Tests\Rules;

class Rule12Test extends RuleBase
{
    protected $ruleToTest = "\UserFrosting\I18n\PluralRules\Rule12";

    /**
     * Families: Semitic (Arabic).
     *
     * 1 - 1
     * 2 - 2
     * 3 - ends in 03-10: 3, 4, ... 10, 103, 104, ... 110, 203, 204, ...
     * 4 - ends in 11-99: 11, ... 99, 111, 112, ...
     * 5 - everything else: 100, 101, 102, 200, 201, 202, ...
     * 6 - 0
     */
    public function ruleProvider()
    {
        return [
            [0, 6],
            [1, 1],
            [2, 2],
            [3, 3],
            [11, 4],
            [12, 4],
            [13, 4],
            [19, 4],
            [20, 4],
            [21, 4],
            [40, 4],
            [100, 5],
            [101, 5],
            [102, 5],
            [103, 3],
            [109, 3],
            [110, 3],
            [111, 4],
            [112, 4],
            [120, 4],
            [121, 4],
            [122, 4],
            [123, 4],
            [124, 4],
            [125, 4],
            [200, 5],
        ];
    }
}
