<?php

/*
 * UserFrosting Framework (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/framework
 * @copyright Copyright (c) 2013-2021 Alexander Weissman, Louis Charette, Jordan Mele
 * @license   https://github.com/userfrosting/framework/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Tests\I18n\Rules;

class Rule4Test extends RuleBase
{
    protected $ruleToTest = "\UserFrosting\I18n\PluralRules\Rule4";

    /**
     * Families: Celtic (Scottish Gaelic)
     * 1 - is 1 or 11: 1, 11
     * 2 - is 2 or 12: 2, 12
     * 3 - others between 3 and 19: 3, 4, ... 10, 13, ... 18, 19
     * 4 - everything else: 0, 20, 21, ...
     */
    public static function ruleProvider()
    {
        return [
            [0, 4],
            [1, 1],
            [2, 2],
            [3, 3],
            [11, 1],
            [12, 2],
            [13, 3],
            [19, 3],
            [20, 4],
            [21, 4],
            [128, 4],
        ];
    }
}
