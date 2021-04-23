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
 * Families: Germanic (Danish, Dutch, English, Faroese, Frisian, German, Norwegian, Swedish), Finno-Ugric (Estonian, Finnish, Hungarian), Language isolate (Basque), Latin/Greek (Greek), Semitic (Hebrew), Romanic (Italian, Portuguese, Spanish, Catalan)
 * 1 - 1
 * 2 - everything else: 0, 2, 3, ...
 *
 * @see https://developer.mozilla.org/en-US/docs/Mozilla/Localization/Localization_and_Plurals#Plural_rule_1_(2_forms)
 */
class Rule1 implements RuleInterface
{
    public static function getRule($number)
    {
        return ($number == 1) ? 1 : 2;
    }
}
