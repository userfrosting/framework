<?php

/*
 * UserFrosting Framework (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/framework
 * @copyright Copyright (c) 2013-2021 Alexander Weissman, Louis Charette, Jordan Mele
 * @license   https://github.com/userfrosting/framework/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Tests\Fortress;

use Exception;
use PHPUnit\Framework\TestCase;
use UserFrosting\Fortress\RequestDataTransformer;
use UserFrosting\Fortress\RequestSchema\RequestSchemaRepository;
use UserFrosting\Support\Repository\Loader\YamlFileLoader;

class RequestDataTransformerTest extends TestCase
{
    protected string $basePath;

    protected RequestDataTransformer $transformer;

    public function setUp(): void
    {
        $this->basePath = __DIR__.'/data';

        // Arrange
        $loader = new YamlFileLoader($this->basePath.'/register.yaml');
        $schema = new RequestSchemaRepository($loader->load());
        $this->transformer = new RequestDataTransformer($schema);
    }

    public function testTransformFieldForNotInSchema(): void
    {
        $schema = new RequestSchemaRepository([
            'email'       => [],
        ]);
        $transformer = new RequestDataTransformer($schema);

        $result = $transformer->transformField('foo', 'bar');
        $this->assertSame('bar', $result);
    }

    /**
     * Basic whitelisting.
     */
    public function testBasic(): void
    {
        // Arrange
        $rawInput = [
            'email'       => 'david@owlfancy.com',
            'admin'       => 1,
            'description' => 'Some stuff to describe',
        ];

        // Arrange
        $schema = new RequestSchemaRepository([
            'email'       => [],
            'description' => null,  // Replicating an input that has no validation operations
        ]);
        $transformer = new RequestDataTransformer($schema);

        // Act
        $result = $transformer->transform($rawInput, 'skip');

        // Assert
        $transformedData = [
            'email'       => 'david@owlfancy.com',
            'description' => 'Some stuff to describe',
        ];

        $this->assertEquals($transformedData, $result);
    }

    public function testBasicWithOnUnexpectedVarAllow(): void
    {
        // Arrange
        $rawInput = [
            'email'       => 'david@owlfancy.com',
            'admin'       => 1,
            'description' => 'Some stuff to describe',
        ];

        // Arrange
        $schema = new RequestSchemaRepository([
            'email'       => [],
            'description' => null,  // Replicating an input that has no validation operations
        ]);
        $transformer = new RequestDataTransformer($schema);

        // Act
        $result = $transformer->transform($rawInput, 'allow');

        // Assert
        $transformedData = [
            'email'       => 'david@owlfancy.com',
            'admin'       => 1,
            'description' => 'Some stuff to describe',
        ];

        $this->assertEquals($transformedData, $result);
    }

    public function testBasicWithOnUnexpectedVarError(): void
    {
        // Arrange
        $rawInput = [
            'email'       => 'david@owlfancy.com',
            'admin'       => 1,
            'description' => 'Some stuff to describe',
        ];

        // Arrange
        $schema = new RequestSchemaRepository([
            'email'       => [],
            'description' => null,  // Replicating an input that has no validation operations
        ]);
        $transformer = new RequestDataTransformer($schema);

        // Set expectations
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("The field 'admin' is not a valid input field.");

        // Act
        $transformer->transform($rawInput, 'error');
    }

    /**
     * "Trim" transformer.
     */
    public function testTrim(): void
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
    public function testEscape(): void
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
    public function testEscapeWithArrayValue(): void
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
    public function testPurge(): void
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
    public function testPurgeWithArrayValue(): void
    {
        // Act
        $rawInput = [
            'user_name' => [
                '<b>My Super-Important Name</b>',
                '<i>My Less-Important Name</i>',
            ],
        ];

        $result = $this->transformer->transform($rawInput, 'skip');

        // Assert
        $transformedData = [
            'email'     => 'david@owlfancy.com',
            'user_name' => ['My Super-Important Name', 'My Less-Important Name'],
        ];

        $this->assertEquals($transformedData, $result);
    }

    /**
     * "Purify" transformer.
     */
    public function testPurify(): void
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

    public function testPurifyWithArrayValue(): void
    {
        // Act
        $rawInput = [
            'puppies' => [
                "<script>I'm definitely really a puppy  </script><b>0</b>",
                "<script>   Trust_me('please');  </script>",
            ],
        ];

        $result = $this->transformer->transform($rawInput, 'skip');

        // Assert
        $transformedData = [
            'email'   => 'david@owlfancy.com',
            'puppies' => ['<b>0</b>', ''],
        ];

        $this->assertEquals($transformedData, $result);
    }

    /**
     * default transformer.
     */
    public function testUnsupportedTransformation(): void
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

    public function testSetters(): void
    {
        $this->assertSame(['email' => 'david@owlfancy.com'], $this->transformer->transform([]));

        // New schema removes default
        $newSchema = new RequestSchemaRepository([
            'email' => [],
        ]);
        $this->transformer->setSchema($newSchema);
        $this->assertSame([], $this->transformer->transform([]));
    }
}
