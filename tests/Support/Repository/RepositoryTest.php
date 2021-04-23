<?php

/*
 * UserFrosting Support (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/support
 * @copyright Copyright (c) 2013-2019 Alexander Weissman
 * @license   https://github.com/userfrosting/support/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Tests\Support\Repository;

use PHPUnit\Framework\TestCase;
use UserFrosting\Support\Repository\Repository;

class RepositoryTest extends TestCase
{
    protected $data = [
        'voles' => [
            'caught'   => 8,
            'devoured' => null,
        ],
        'plumage'     => null,
        'chicks'      => 4,
        'in_flight'   => false,
        'name'        => '',
        'chick_names' => [],
    ];

    public function testGetDefined()
    {
        $repo = new Repository($this->data);

        $defined = $repo->getDefined();

        $this->assertEquals([
            'voles' => [
                'caught' => 8,
            ],
            'chicks'      => 4,
            'in_flight'   => false,
            'name'        => '',
            'chick_names' => [],
        ], $defined);
    }

    public function testGetDefinedSubkey()
    {
        $repo = new Repository($this->data);

        $defined = $repo->getDefined('voles');

        $this->assertEquals([
            'caught' => 8,
        ], $defined);
    }

    /**
     * @depends testGetDefined
     */
    public function testGetDefinedWithString()
    {
        $repo = new Repository($this->data);

        $defined = $repo->getDefined('chicks');

        $this->assertEquals(4, $defined);
    }

    /**
     * @depends testGetDefined
     */
    public function testGetDefinedWithArray()
    {
        $repo = new Repository($this->data);

        $defined = $repo->getDefined(['chicks', 'voles']);

        $this->assertEquals([
            'chicks' => 4,
            'voles'  => [
                'caught' => 8,
            ],
        ], $defined);
    }

    /**
     * @depends testGetDefinedWithString
     */
    public function testMergeItems()
    {
        $repo = new Repository($this->data);

        $repo->mergeItems('foo', 'bar');
        $defined = $repo->getDefined('foo');

        $this->assertEquals('bar', $defined);
    }

    /**
     * @depends testGetDefinedWithString
     */
    public function testMergeItemsWithArray()
    {
        $repo = new Repository($this->data);

        $newData = [
            'caught' => 4,
            'foo'    => 'bar',
        ];

        $repo->mergeItems('voles', $newData);
        $defined = $repo->getDefined('voles');

        $this->assertEquals($newData, $defined);
    }

    /**
     * @depends testGetDefinedWithString
     */
    public function testMergeItemsWithNull()
    {
        $repo = new Repository($this->data);

        $newData = [
            'foo'    => 'bar',
        ];

        $repo->mergeItems(null, $newData);
        $defined = $repo->getDefined('foo');

        $this->assertEquals('bar', $defined);
    }
}
