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
 * Families: Celtic (Irish Gaeilge)
 * 1 - 1
 * 2 - 2
 * 3 - is 3-6: 3, 4, 5, 6
 * 4 - is 7-10: 7, 8, 9, 10
 * 5 - everything else: 0, 11, 12, ...
 *
 * @see https://developer.mozilla.org/en-US/docs/Mozilla/Localization/Localization_and_Plurals#Plural_rule_11_(5_forms)
 */
class Rule11 implements RuleInterface
{
    public static function getRule($number)
    {
        if ($number == 1) {
            return 1;
        }

        if ($number == 2) {
            return 2;
        }

        if ($number >= 3 && $number <= 6) {
            return 3;
        }

        if ($number >= 7 && $number <= 10) {
            return 4;
        }

        return 5;
    }
}
