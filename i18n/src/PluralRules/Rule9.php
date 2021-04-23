<?php

/*
 * UserFrosting i18n (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/i18n
 * @copyright Copyright (c) 2013-2019 Alexander Weissman, Louis Charette
 * @license   https://github.com/userfrosting/i18n/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\I18n\PluralRules;

/**
 * Families: Slavic (Polish)
 * 1 - 1
 * 2 - ends in 2-4, not 12-14: 2, 3, 4, 22, 23, 24, 32, ... 104, 122, ...
 * 3 - everything else: 0, 5, 6, ... 11, 12, 13, 14, 15, ... 20, 21, 25, ...
 *
 * @see https://developer.mozilla.org/en-US/docs/Mozilla/Localization/Localization_and_Plurals#Plural_rule_9_(3_forms)
 */
class Rule9 implements RuleInterface
{
    public static function getRule($number)
    {
        if ($number == 1) {
            return 1;
        }

        if (($number % 10 >= 2) && ($number % 10 <= 4) && (($number % 100 < 12) || ($number % 100 > 14))) {
            return 2;
        }

        return 3;
    }
}
