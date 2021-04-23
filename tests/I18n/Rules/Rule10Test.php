<?php

/*
 * UserFrosting Framework (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/framework
 * @copyright Copyright (c) 2013-2021 Alexander Weissman, Louis Charette, Jordan Mele
 * @license   https://github.com/userfrosting/framework/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Tests\I18n\Rules;

class Rule10Test extends RuleBase
{
    protected $ruleToTest = "\UserFrosting\I18n\PluralRules\Rule10";

    /**
     * Families: Slavic (Slovenian, Sorbian)
     * 1 - ends in 01: 1, 101, 201, ...
     * 2 - ends in 02: 2, 102, 202, ...
     * 3 - ends in 03-04: 3, 4, 103, 104, 203, 204, ...
     * 4 - everything else: 0, 5, 6, 7, 8, 9, 10, 11, ...
     */
    public function ruleProvider()
    {
        return [
            [0, 4],
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
            [100, 4],
            [101, 1],
            [102, 2],
            [120, 4],
            [121, 4],
            [122, 4],
            [123, 4],
            [124, 4],
            [125, 4],
            [201, 1],
            [202, 2],
            [203, 3],
            [204, 3],
            [205, 4],
        ];
    }
}
