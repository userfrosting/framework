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
 * Interface for Rule Definition.
 *
 * The plural rules are based on a list published by the Mozilla Developer Network & code from phpBB Group
 *
 * @see https://developer.mozilla.org/en-US/docs/Mozilla/Localization/Localization_and_Plurals
 */
interface RuleInterface
{
    /**
     * Return the rule to apply.
     *
     * @param int $number The number we want the rule for
     *
     * @return int The rule
     */
    public static function getRule($number);
}
