<?php

/*
 * UserFrosting i18n (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/i18n
 * @copyright Copyright (c) 2013-2019 Alexander Weissman, Louis Charette
 * @license   https://github.com/userfrosting/i18n/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\I18n\Tests\Rules;

class Rule3Test extends RuleBase
{
    protected $ruleToTest = "\UserFrosting\I18n\PluralRules\Rule3";

    /**
     * Families: Baltic (Latvian)
     * 1 - 0
     * 2 - ends in 1, not 11: 1, 21, ... 101, 121, ...
     * 3 - everything else: 2, 3, ... 10, 11, 12, ... 20, 22, ...
     */
    public function ruleProvider()
    {
        return [
            [0, 1],
            [1, 2],
            [2, 3],
            [11, 3],
            [21, 2],
            [141, 2],
            [128, 3],
        ];
    }
}
