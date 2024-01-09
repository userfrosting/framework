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
use UserFrosting\Support\Exception\FileNotFoundException;

class RequestSchemaTest extends TestCase
{
    protected string $schemaPath;

    public function setUp(): void
    {
        $this->schemaPath = __DIR__.'/data/contact.json';
    }

    public function testWithNoPath(): void
    {
        $requestSchema = new RequestSchema();
        $this->assertSame([], $requestSchema->all());
    }

    public function testWithPath(): void
    {
        $contactSchema = [
            'message' => [
                'validators' => [
                    'required' => [
                        'message' => 'Please enter a message',
                    ],
                ],
            ],
        ];

        $requestSchema = new RequestSchema($this->schemaPath);
        $this->assertSame($contactSchema['message'], $requestSchema->all()['message']);
    }

    public function testWithBadPath(): void
    {
        $this->expectException(FileNotFoundException::class);
        new RequestSchema(__DIR__.'/data/bad.json');
    }
}
