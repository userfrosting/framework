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
 * Families: Slavic (Croatian, Serbian, Russian, Ukrainian)
 * 1 - ends in 1, not 11: 1, 21, 31, ... 101, 121, ...
 * 2 - ends in 2-4, not 12-14: 2, 3, 4, 22, 23, 24, 32, ...
 * 3 - everything else: 0, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 25, 26, ...
 *
 * @see https://developer.mozilla.org/en-US/docs/Mozilla/Localization/Localization_and_Plurals#Plural_rule_7_(3_forms)
 */
class Rule7 implements RuleInterface
{
    public static function getRule($number)
    {
        if (($number % 10 == 1) && ($number % 100 != 11)) {
            return 1;
        }

        if (($number % 10 >= 2) && ($number % 10 <= 4) && (($number % 100 < 10) || ($number % 100 >= 20))) {
            return 2;
        }

        return 3;
    }
}
