<?php

/*
 * UserFrosting Framework (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/framework
 * @copyright Copyright (c) 2013-2021 Alexander Weissman, Louis Charette, Jordan Mele
 * @license   https://github.com/userfrosting/framework/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\I18n\PluralRules;

/**
 * Families: Romanic (French, Brazilian Portuguese)
 * 1 - 0, 1
 * 2 - everything else: 2, 3, ...
 *
 * @see https://developer.mozilla.org/en-US/docs/Mozilla/Localization/Localization_and_Plurals#Plural_rule_2_(2_forms)
 */
class Rule2 implements RuleInterface
{
    public static function getRule($number)
    {
        if ($number == 0 || $number == 1) {
            return 1;
        }

        return 2;
    }
}
