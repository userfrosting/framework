<?php

/*
 * UserFrosting Framework (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/framework
 * @copyright Copyright (c) 2013-2021 Alexander Weissman, Louis Charette, Jordan Mele
 * @license   https://github.com/userfrosting/framework/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Fortress;

use Exception;
use UserFrosting\Fortress\RequestSchema\RequestSchemaInterface;

/**
 * RequestDataTransformer Class.
 *
 * Perform a series of transformations on a set of data fields, as specified by a RequestSchemaInterface.
 *
 * @author Alex Weissman (https://alexanderweissman.com)
 */
class RequestDataTransformer implements RequestDataTransformerInterface
{
    /**
     * @var RequestSchemaInterface
     */
    protected $schema;

    /**
     * @var \HTMLPurifier
     */
    protected $purifier;

    /**
     * Create a new data transformer.
     *
     * @param RequestSchemaInterface $schema A RequestSchemaInterface object, containing the transformation rules.
     */
    public function __construct(RequestSchemaInterface $schema)
    {
        // Create purifier
        $config = \HTMLPurifier_Config::createDefault();
        $config->set('Cache.DefinitionImpl', null); // turn off cache
        $this->purifier = new \HTMLPurifier($config);

        // Set schema
        $this->setSchema($schema);
    }

    /**
     * Set the schema for this transformer, as a valid RequestSchemaInterface object.
     *
     * @param RequestSchemaInterface $schema A RequestSchemaInterface object, containing the transformation rules.
     */
    public function setSchema(RequestSchemaInterface $schema)
    {
        $this->schema = $schema;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function transform(array $data, $onUnexpectedVar = 'skip')
    {
        $schemaFields = $this->schema->all();

        // 1. Perform sequence of transformations on each field.
        $transformedData = [];
        foreach ($data as $name => $value) {
            // Handle values not listed in the schema
            if (!array_key_exists($name, $schemaFields)) {
                switch ($onUnexpectedVar) {
                    case 'allow':
                        $transformedData[$name] = $value;
                        break;
                    case 'error':
                        // TODO : BadRequestException temp replacement
                        $e = new Exception("The field '$name' is not a valid input field.");

                        throw $e;
                        break;
                    case 'skip':
                    default:
                        continue 2;
                        break;
                }
            } else {
                $transformedData[$name] = $this->transformField($name, $value);
            }
        }

        // 2. Get default values for any fields missing from $data.  Especially useful for checkboxes, etc which are not submitted when they are unchecked
        foreach ($this->schema->all() as $fieldName => $field) {
            if (!isset($transformedData[$fieldName])) {
                if (isset($field['default'])) {
                    $transformedData[$fieldName] = $field['default'];
                }
            }
        }

        return $transformedData;
    }

    /**
     * {@inheritdoc}
     */
    public function transformField($name, $value)
    {
        $schemaFields = $this->schema->all();

        $fieldParameters = $schemaFields[$name];

        if (!isset($fieldParameters['transformations']) || empty($fieldParameters['transformations'])) {
            return $value;
        } else {
            // Field exists in schema, so apply sequence of transformations
            $transformedValue = $value;

            foreach ($fieldParameters['transformations'] as $transformation) {
                switch (strtolower($transformation)) {
                    case 'purify':
                        $transformedValue = $this->purifier->purify($transformedValue);
                        break;
                    case 'escape':
                        $transformedValue = $this->escapeHtmlCharacters($transformedValue);
                        break;
                    case 'purge':
                        $transformedValue = $this->purgeHtmlCharacters($transformedValue);
                        break;
                    case 'trim':
                        $transformedValue = $this->trim($transformedValue);
                        break;
                    default:
                        break;
                }
            }

            return $transformedValue;
        }
    }

    /**
     * Autodetect if a field is an array or scalar, and filter appropriately.
     *
     * @param string|string[] $value
     *
     * @return string|string[]
     */
    protected function escapeHtmlCharacters(string|array $value): string|array
    {
        if (is_array($value)) {
            return filter_var_array($value, FILTER_SANITIZE_SPECIAL_CHARS);
        }

        return filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);
    }

    /**
     * Autodetect if a field is an array or scalar, and filter appropriately.
     *
     * @param string|string[] $value
     *
     * @return string|string[]
     */
    protected function purgeHtmlCharacters(string|array $value): string|array
    {
        if (is_array($value)) {
            return array_map('strip_tags', $value);
        }

        return strip_tags($value);
    }

    /**
     * Autodetect if a field is an array or scalar, and filter appropriately.
     *
     * @param string|string[] $value
     *
     * @return string|string[]
     */
    protected function trim(string|array $value): string|array
    {
        if (is_array($value)) {
            return array_map('trim', $value);
        }

        return trim($value);
    }
}
