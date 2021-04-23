<?php

/*
 * UserFrosting Framework (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/framework
 * @copyright Copyright (c) 2013-2021 Alexander Weissman, Louis Charette, Jordan Mele
 * @license   https://github.com/userfrosting/framework/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Tests\Fortress;

use PHPUnit\Framework\TestCase;
use UserFrosting\Fortress\RequestSchema;

class RequestSchemaTest extends TestCase
{
    public function setUp(): void
    {
        $this->basePath = __DIR__.'/data/contact.json';

        $this->contactSchema = [
            'message' => [
                'validators' => [
                    'required' => [
                        'message' => 'Please enter a message',
                    ],
                ],
            ],
        ];
    }

    public function testWithNoPath()
    {
        $requestSchema = new RequestSchema();
        $this->assertSame([], $requestSchema->all());
        $this->assertSame($requestSchema->all(), $requestSchema->all());
    }

    public function testWithPath()
    {
        $requestSchema = new RequestSchema($this->basePath);
        $this->assertSame($this->contactSchema['message'], $requestSchema->all()['message']);
        $this->assertSame($requestSchema->all(), $requestSchema->all());
    }

    /**
     * Test depreated code.
     *
     * @depends testWithPath
     */
    public function testDeprecatedSupport()
    {
        $requestSchema = new RequestSchema($this->basePath);
        $this->assertSame($requestSchema->all(), $requestSchema->getSchema());
    }
}
