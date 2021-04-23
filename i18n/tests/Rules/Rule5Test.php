<?php

/*
 * UserFrosting i18n (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/i18n
 * @copyright Copyright (c) 2013-2019 Alexander Weissman, Louis Charette
 * @license   https://github.com/userfrosting/i18n/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\I18n\Tests\Rules;

class Rule5Test extends RuleBase
{
    protected $ruleToTest = "\UserFrosting\I18n\PluralRules\Rule5";

    /**
     * Families: Romanic (Romanian)
     * 1 - 1
     * 2 - is 0 or ends in 01-19: 0, 2, 3, ... 19, 101, 102, ... 119, 201, ...
     * 3 - everything else: 20, 21, ...
     */
    public function ruleProvider()
    {
        return [
            [0, 2],
            [1, 1],
            [2, 2],
            [3, 2],
            [11, 2],
            [12, 2],
            [13, 2],
            [19, 2],
            [20, 3],
            [21, 3],
            [100, 3],
            [101, 2],
            [110, 2],
            [111, 2],
            [128, 3],
        ];
    }
}
