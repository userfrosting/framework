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
        'array'  => ['foo', 'bar'],
    ];

    public function testGetBool(): void
    {
        $repo = new Config($this->data);

        $this->assertSame(true, $repo->getBool('bool'));
        $this->assertSame(false, $repo->getBool('missing', false));
        $this->assertSame(null, $repo->getBool('missing'));
        $this->assertSame($repo->get('missing'), $repo->getBool('missing')); // Same default behavior as "get"
        $this->expectException(TypeException::class);
        $repo->getBool('string');
    }

    public function testGetString(): void
    {
        $repo = new Config($this->data);

        $this->assertSame('foobar', $repo->getString('string'));
        $this->assertSame('barfoo', $repo->getString('missing', 'barfoo'));
        $this->assertSame(null, $repo->getString('missing'));
        $this->assertSame($repo->get('missing'), $repo->getString('missing')); // Same default behavior as "get"
        $this->expectException(TypeException::class);
        $repo->getString('bool');
    }

    public function testGetInt(): void
    {
        $repo = new Config($this->data);

        $this->assertSame(92, $repo->getInt('int'));
        $this->assertSame(29, $repo->getInt('missing', 29));
        $this->assertSame(null, $repo->getInt('missing'));
        $this->assertSame($repo->get('missing'), $repo->getInt('missing')); // Same default behavior as "get"
        $this->expectException(TypeException::class);
        $repo->getInt('string');
    }

    public function testGetArray(): void
    {
        $repo = new Config($this->data);

        $this->assertSame(['foo', 'bar'], $repo->getArray('array'));
        $this->assertSame(['bar', 'foo'], $repo->getArray('missing', ['bar', 'foo']));
        $this->assertSame(null, $repo->getArray('missing'));
        $this->assertSame($repo->get('missing'), $repo->getArray('missing')); // Same default behavior as "get"
        $this->expectException(TypeException::class);
        $repo->getArray('string');
    }
}
