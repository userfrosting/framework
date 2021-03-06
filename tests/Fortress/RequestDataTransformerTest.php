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
use UserFrosting\Fortress\RequestDataTransformer;
use UserFrosting\Fortress\RequestSchema\RequestSchemaRepository;
use UserFrosting\Support\Exception\BadRequestException;
use UserFrosting\Support\Repository\Loader\YamlFileLoader;

class RequestDataTransformerTest extends TestCase
{
    protected $basePath;

    protected $transformer;

    public function setUp(): void
    {
        $this->basePath = __DIR__.'/data';

        // Arrange
        $loader = new YamlFileLoader($this->basePath.'/register.yaml');
        $schema = new RequestSchemaRepository($loader->load());
        $this->transformer = new RequestDataTransformer($schema);
    }

    /**
     * Basic whitelisting.
     */
    public function testBasic()
    {
        // Arrange
        $rawInput = [
            'email'       => 'david@owlfancy.com',
            'admin'       => 1,
            'description' => 'Some stuff to describe',
        ];

        // Arrange
        $schema = new RequestSchemaRepository();

        $schema->mergeItems(null, [
            'email'       => [],
            'description' => null,  // Replicating an input that has no validation operations
        ]);
        $this->transformer = new RequestDataTransformer($schema);

        // Act
        $result = $this->transformer->transform($rawInput, 'skip');

        // Assert
        $transformedData = [
            'email'       => 'david@owlfancy.com',
            'description' => 'Some stuff to describe',
        ];

        $this->assertEquals($transformedData, $result);
    }

    public function testBasicWithOnUnexpectedVarAllow()
    {
        // Arrange
        $rawInput = [
            'email'       => 'david@owlfancy.com',
            'admin'       => 1,
            'description' => 'Some stuff to describe',
        ];

        // Arrange
        $schema = new RequestSchemaRepository();

        $schema->mergeItems(null, [
            'email'       => [],
            'description' => null,  // Replicating an input that has no validation operations
        ]);
        $this->transformer = new RequestDataTransformer($schema);

        // Act
        $result = $this->transformer->transform($rawInput, 'allow');

        // Assert
        $transformedData = [
            'email'       => 'david@owlfancy.com',
            'admin'       => 1,
            'description' => 'Some stuff to describe',
        ];

        $this->assertEquals($transformedData, $result);
    }

    public function testBasicWithOnUnexpectedVarError()
    {
        // Arrange
        $rawInput = [
            'email'       => 'david@owlfancy.com',
            'admin'       => 1,
            'description' => 'Some stuff to describe',
        ];

        // Arrange
        $schema = new RequestSchemaRepository();

        $schema->mergeItems(null, [
            'email'       => [],
            'description' => null,  // Replicating an input that has no validation operations
        ]);
        $this->transformer = new RequestDataTransformer($schema);

        // Set expectations
        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage("The field 'admin' is not a valid input field.");

        // Act
        $this->transformer->transform($rawInput, 'error');
    }

    /**
     * "Trim" transformer.
     */
    public function testTrim()
    {
        // Act
        $rawInput = [
            'display_name' => 'THE GREATEST  ',
        ];

        $result = $this->transformer->transform($rawInput, 'skip');

        // Assert
        $transformedData = [
            'email'        => 'david@owlfancy.com',
            'display_name' => 'THE GREATEST',
        ];

        $this->assertEquals($transformedData, $result);
    }

    /**
     * "Escape" transformer.
     */
    public function testEscape()
    {
        // Act
        $rawInput = [
            'display_name' => '<b>My Super-Important Name</b>',
        ];

        $result = $this->transformer->transform($rawInput, 'skip');

        // Assert
        $transformedData = [
            'email'        => 'david@owlfancy.com',
            'display_name' => '&#60;b&#62;My Super-Important Name&#60;/b&#62;',
        ];

        $this->assertEquals($transformedData, $result);
    }

    /**
     * @depends testEscape
     */
    public function testEscapeWithArrayValue()
    {
        // Act
        $rawInput = [
            'display_name' => ['<b>My Super-Important Name</b>'],
        ];

        $result = $this->transformer->transform($rawInput, 'skip');

        // Assert
        $transformedData = [
            'email'        => 'david@owlfancy.com',
            'display_name' => ['&#60;b&#62;My Super-Important Name&#60;/b&#62;'],
        ];

        $this->assertEquals($transformedData, $result);
    }

    /**
     * "Purge" transformer.
     */
    public function testPurge()
    {
        // Act
        $rawInput = [
            'user_name' => '<b>My Super-Important Name</b>',
        ];

        $result = $this->transformer->transform($rawInput, 'skip');

        // Assert
        $transformedData = [
            'email'     => 'david@owlfancy.com',
            'user_name' => 'My Super-Important Name',
        ];

        $this->assertEquals($transformedData, $result);
    }

    /**
     * @depends testPurge
     */
    public function testPurgeWithArrayValue()
    {
        // Act
        $rawInput = [
            'user_name' => ['<b>My Super-Important Name</b>'],
        ];

        $result = $this->transformer->transform($rawInput, 'skip');

        // Assert
        $transformedData = [
            'email'     => 'david@owlfancy.com',
            'user_name' => ['My Super-Important Name'],
        ];

        $this->assertEquals($transformedData, $result);
    }

    /**
     * "Purify" transformer.
     */
    public function testPurify()
    {
        // Act
        $rawInput = [
            'puppies' => "<script>I'm definitely really a puppy  </script><b>0</b>",
        ];

        $result = $this->transformer->transform($rawInput, 'skip');

        // Assert
        $transformedData = [
            'email'   => 'david@owlfancy.com',
            'puppies' => '<b>0</b>',
        ];

        $this->assertEquals($transformedData, $result);
    }

    /**
     * default transformer.
     */
    public function testUnsuportedTransformation()
    {
        // Act
        $rawInput = [
            'kitties' => '<b>My Super-Important Test</b>',
        ];

        $result = $this->transformer->transform($rawInput, 'skip');

        // Assert
        $transformedData = [
            'email'   => 'david@owlfancy.com',
            'kitties' => '<b>My Super-Important Test</b>',
        ];

        $this->assertEquals($transformedData, $result);
    }
}
