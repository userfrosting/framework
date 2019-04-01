<?php

/*
 * UserFrosting Support (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/support
 * @copyright Copyright (c) 2013-2019 Alexander Weissman
 * @license   https://github.com/userfrosting/support/blob/master/LICENSE.md (MIT License)
 */

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
}
