<?php

/*
 * UserFrosting i18n (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/i18n
 * @copyright Copyright (c) 2013-2019 Alexander Weissman, Louis Charette
 * @license   https://github.com/userfrosting/i18n/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\I18n\Tests\Rules;

use PHPUnit\Framework\TestCase;
use UserFrosting\I18n\PluralRules\RuleInterface;

abstract class RuleBase extends TestCase
{
    /** @var string Rule number to test. Reference to instance of \UserFrosting\I18n\PluralRules\RuleInterface */
    protected $ruleToTest;

    /**
     * Test rule class implement the right interface.
     */
    public function testRuleClass()
    {
        $this->assertInstanceOf(RuleInterface::class, new $this->ruleToTest());
    }

    /**
     * @dataProvider ruleProvider
     *
     * @param int $number         Input number
     * @param int $expectedResult Expected result
     */
    public function testRule($number, $expectedResult)
    {
        $rule = $this->ruleToTest;
        $result = $rule::getRule($number);
        $this->assertSame($expectedResult, $result);
    }

    /**
     * Data provider for `testRule`.
     *
     * @return array
     */
    abstract public function ruleProvider();
}
