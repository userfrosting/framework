<?php

/*
 * UserFrosting Framework (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/framework
 * @copyright Copyright (c) 2013-2021 Alexander Weissman, Louis Charette, Jordan Mele
 * @license   https://github.com/userfrosting/framework/blob/master/LICENSE.md (MIT License)
 */

use PHPUnit\Framework\TestCase;
use UserFrosting\Config\Config;
use UserFrosting\Config\TypeException;

class ConfigTest extends TestCase
{
    /** @var array<string,mixed> */
    protected array $data = [
        'bool'   => true,
        'string' => 'foobar',
        'int'    => 92,
        'array'  => [],
    ];

    public function testGetBool(): void
    {
        $repo = new Config($this->data);

        $this->assertSame(true, $repo->getBool('bool'));
        $this->expectException(TypeException::class);
        $repo->getBool('string');
    }

    public function testGetString(): void
    {
        $repo = new Config($this->data);

        $this->assertSame('foobar', $repo->getString('string'));
        $this->expectException(TypeException::class);
        $repo->getString('bool');
    }

    public function testGetInt(): void
    {
        $repo = new Config($this->data);

        $this->assertSame(92, $repo->getInt('int'));
        $this->expectException(TypeException::class);
        $repo->getInt('string');
    }

    public function testGetArray(): void
    {
        $repo = new Config($this->data);

        $this->assertSame([], $repo->getArray('array'));
        $this->expectException(TypeException::class);
        $repo->getArray('string');
    }
}
