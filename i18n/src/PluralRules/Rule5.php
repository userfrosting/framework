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
 * Families: Romanic (Romanian)
 * 1 - 1
 * 2 - is 0 or ends in 01-19: 0, 2, 3, ... 19, 101, 102, ... 119, 201, ...
 * 3 - everything else: 20, 21, ...
 *
 * @see https://developer.mozilla.org/en-US/docs/Mozilla/Localization/Localization_and_Plurals#Plural_rule_5_(3_forms)
 */
class Rule5 implements RuleInterface
{
    public static function getRule($number)
    {
        if ($number == 1) {
            return 1;
        }

        if ($number == 0 || (($number % 100 > 0) && ($number % 100 < 20))) {
            return 2;
        }

        return 3;
    }
}
