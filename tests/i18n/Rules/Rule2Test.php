<?php

/*
 * UserFrosting i18n (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/i18n
 * @copyright Copyright (c) 2013-2019 Alexander Weissman, Louis Charette
 * @license   https://github.com/userfrosting/i18n/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\I18n\Tests\Rules;

class Rule2Test extends RuleBase
{
    protected $ruleToTest = "\UserFrosting\I18n\PluralRules\Rule2";

    /**
     * Families: Romanic (French, Brazilian Portuguese)
     * 1 - 0, 1
     * 2 - everything else: 2, 3, ...
     */
    public function ruleProvider()
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
