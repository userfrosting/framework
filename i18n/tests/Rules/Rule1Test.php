<?php

/*
 * UserFrosting i18n (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/i18n
 * @copyright Copyright (c) 2013-2019 Alexander Weissman, Louis Charette
 * @license   https://github.com/userfrosting/i18n/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\I18n\Tests\Rules;

class Rule1Test extends RuleBase
{
    protected $ruleToTest = "\UserFrosting\I18n\PluralRules\Rule1";

    /**
     * Families: Germanic (Danish, Dutch, English, Faroese, Frisian, German, Norwegian, Swedish), Finno-Ugric (Estonian, Finnish, Hungarian), Language isolate (Basque), Latin/Greek (Greek), Semitic (Hebrew), Romanic (Italian, Portuguese, Spanish, Catalan)
     * 1 - 1
     * 2 - everything else: 0, 2, 3, ...
     */
    public function ruleProvider()
    {
        return [
            [0, 2],
            [1, 1],
            [2, 2],
            [-2, 2],
            [128, 2],
        ];
    }
}
