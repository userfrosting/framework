<?php

/*
 * UserFrosting i18n (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/i18n
 * @copyright Copyright (c) 2013-2019 Alexander Weissman, Louis Charette
 * @license   https://github.com/userfrosting/i18n/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\I18n\Tests\Rules;

class Rule14Test extends RuleBase
{
    protected $ruleToTest = "\UserFrosting\I18n\PluralRules\Rule14";

    /**
     * Families: Slavic (Macedonian)
     * 1 - ends in 1: 1, 11, 21, ...
     * 2 - ends in 2: 2, 12, 22, ...
     * 3 - everything else: 0, 3, 4, ... 10, 13, 14, ... 20, 23, ...
     */
    public function ruleProvider()
    {
        return [
            [0, 3],
            [1, 1],
            [2, 2],
            [3, 3],
            [11, 1],
            [12, 2],
            [13, 3],
            [19, 3],
            [20, 3],
            [21, 1],
            [40, 3],
            [100, 3],
            [101, 1],
            [102, 2],
            [103, 3],
            [109, 3],
            [110, 3],
            [111, 1],
            [112, 2],
            [120, 3],
            [121, 1],
            [122, 2],
            [123, 3],
            [124, 3],
            [125, 3],
            [200, 3],
        ];
    }
}
