<?php

/*
 * UserFrosting Framework (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/framework
 * @copyright Copyright (c) 2013-2021 Alexander Weissman, Louis Charette, Jordan Mele
 * @license   https://github.com/userfrosting/framework/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Tests\I18n\Rules;

class Rule2Test extends RuleBase
{
    protected $ruleToTest = "\UserFrosting\I18n\PluralRules\Rule2";

    /**
     * Families: Romanic (French, Brazilian Portuguese)
     * 1 - 0, 1
     * 2 - everything else: 2, 3, ...
     */
    public static function ruleProvider()
    {
        return [
            [0, 1],
            [1, 1],
            [2, 2],
            [-2, 2],
            [128, 2],
        ];
    }
}
