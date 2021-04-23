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
 * Families: Slavic (Slovak, Czech)
 * 1 - 1
 * 2 - 2, 3, 4
 * 3 - everything else: 0, 5, 6, 7, ...
 *
 * @see https://developer.mozilla.org/en-US/docs/Mozilla/Localization/Localization_and_Plurals#Plural_rule_8_(3_forms)
 */
class Rule8 implements RuleInterface
{
    public static function getRule($number)
    {
        if ($number == 1) {
            return 1;
        }

        if ($number >= 2 && $number <= 4) {
            return 2;
        }

        return 3;
    }
}
