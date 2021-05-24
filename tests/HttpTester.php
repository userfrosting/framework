<?php

/*
 * UserFrosting Framework (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/framework
 * @copyright Copyright (c) 2013-2021 Alexander Weissman, Louis Charette, Jordan Mele
 * @license   https://github.com/userfrosting/framework/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Tests;

use PHPUnit\Framework\MockObject\MockObject;

/**
 * HttpTester Trait.
 *
 * @see https://odan.github.io/2020/06/09/slim4-testing.html#http-tests
 */
trait HttpTester
{
    /**
     * Add mock to container.
     *
     * @param string $class The class or interface
     *
     * @return MockObject The mock
     */
    // TODO Enabled if necessary and move to trait
    /*protected function mock(string $class): MockObject
    {
        if (!class_exists($class)) {
            throw new InvalidArgumentException(sprintf('Class not found: %s', $class));
        }

        $mock = $this->getMockBuilder($class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->container->set($class, $mock);

        return $mock;
    }*/

    /**
     * Asserts that collections are equivalent.
     *
     * @param  array                                   $expected
     * @param  array                                   $actual
     * @param  string                                  $key      [description]
     * @param  string                                  $message  [description]
     * @throws \PHPUnit_Framework_AssertionFailedError
     */
    // TODO : Move to Trait
    /*public static function assertCollectionsSame($expected, $actual, $key = 'id', $message = '')
    {
        // Check that they have the same number of items
        static::assertEquals(count($expected), count($actual));

        // Sort by primary key
        $expected = collect($expected)->sortBy($key);
        $actual = collect($actual)->sortBy($key);

        // Check that the keys match
        $expectedKeys = $expected->keys()->all();
        $actualKeys = $actual->keys()->all();
        static::assertEquals(sort($expectedKeys), sort($actualKeys));

        // Check that the array representations of each collection item match
        $expected = $expected->values();
        $actual = $actual->values();
        for ($i = 0; $i < count($expected); $i++) {
            static::assertEquals(
                static::castToComparable($expected[$i]),
                static::castToComparable($actual[$i])
            );
        }
    }*/

    /**
     * Call protected/private method of a class.
     *
     * @param  object &$object    Instantiated object that we will run method on.
     * @param  string $methodName Method name to call
     * @param  array  $parameters Array of parameters to pass into method.
     * @return mixed  Method return.
     */
    // TODO : Move to Trait
    /*public function invokeMethod(&$object, $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }*/

    /**
     * Helpers
     */

    /**
     * Cast an item to an array if it has a toArray() method.
     *
     * @param  object $item
     * @return mixed
     */
    // TODO : Move to Trait
    /*protected static function castToComparable($item)
    {
        return (is_object($item) && method_exists($item, 'toArray')) ? $item->toArray() : $item;
    }*/

    /**
     * Remove all relations on a collection of models.
     *
     * @param array $models
     */
    // TODO : Move to Trait
    /*protected static function ignoreRelations($models)
    {
        foreach ($models as $model) {
            $model->setRelations([]);
        }
    }*/

    /**
     * cloneObjectArray
     *
     * @param  array $original
     * @return array
     */
    // TODO : Move to Trait
    /*protected function cloneObjectArray($original)
    {
        $cloned = [];

        foreach ($original as $k => $v) {
            $cloned[$k] = clone $v;
        }

        return $cloned;
    }*/
}
