<?php

declare(strict_types=1);

/*
 * UserFrosting Framework (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/framework
 * @copyright Copyright (c) 2013-2021 Alexander Weissman, Louis Charette, Jordan Mele
 * @license   https://github.com/userfrosting/framework/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Fortress\Transformer;

use HTMLPurifier;
use HTMLPurifier_Config;
use UserFrosting\Fortress\FortressException;
use UserFrosting\Fortress\RequestSchema\RequestSchemaInterface;

/**
 * Perform a series of transformations on a set of data fields, as specified by
 * a RequestSchemaInterface.
 */
final class RequestDataTransformer implements RequestDataTransformerInterface
{
    /**
     * @var HTMLPurifier
     */
    protected HTMLPurifier $purifier;

    /**
     * Create a new data transformer.
     */
    public function __construct()
    {
        // Create purifier
        $config = HTMLPurifier_Config::createDefault();
        $config->set('Cache.DefinitionImpl', null); // turn off cache
        $this->purifier = new HTMLPurifier($config);
    }

    /**
     * {@inheritdoc}
     */
    public function transform(
        RequestSchemaInterface $schema,
        array $data,
        string $onUnexpectedVar = 'skip'
    ): array {
        // Get schema fields
        $schemaFields = $schema->all();

        // 1. Perform sequence of transformations on each field.
        $transformedData = [];
        foreach ($data as $name => $value) {
            // Handle values not listed in the schema. Pass not found to
            // transformField if allow is set, transformField will return the value as is.
            if (array_key_exists($name, $schemaFields) || $onUnexpectedVar === 'allow') {
                $transformedData[$name] = $this->transformField($schema, $name, $value);
            } elseif ($onUnexpectedVar === 'error') {
                $e = new FortressException("The field '$name' is not a valid input field.");

                throw $e;
            }
        }

        // 2. Get default values for any fields missing from $data.  Especially useful for checkboxes, etc which are not submitted when they are unchecked
        foreach ($schemaFields as $fieldName => $field) {
            if (!isset($transformedData[$fieldName]) && isset($field['default'])) {
                $transformedData[$fieldName] = $field['default'];
            }
        }

        return $transformedData;
    }

    /**
     * {@inheritdoc}
     */
    public function transformField(RequestSchemaInterface $schema, string $name, mixed $value): mixed
    {
        $schemaFields = $schema->all();

        // Return value if field is not in schema
        if (!array_key_exists($name, $schemaFields)) {
            return $value;
        }

        $fieldParameters = $schemaFields[$name];

        if (!isset($fieldParameters['transformations']) || !is_array($fieldParameters['transformations'])) {
            return $value;
        } else {
            // Field exists in schema, so apply sequence of transformations
            $transformedValue = $value;
            foreach ($fieldParameters['transformations'] as $transformation) {
                $transformedValue = match (strtolower($transformation)) {
                    'purify' => $this->purify($transformedValue),
                    'escape' => $this->escapeHtmlCharacters($transformedValue),
                    'purge'  => $this->purgeHtmlCharacters($transformedValue),
                    'trim'   => $this->trim($transformedValue),
                    default  => $transformedValue,
                };
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
            return filter_var_array($value, FILTER_SANITIZE_SPECIAL_CHARS); // @phpstan-ignore-line
        }

        return filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS); // @phpstan-ignore-line
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

    /**
     * Autodetect if a field is an array or scalar, and filter appropriately.
     *
     * @param string|string[] $value
     *
     * @return string|string[]
     */
    protected function purify(string|array $value): string|array
    {
        if (is_array($value)) {
            return array_map([$this->purifier, 'purify'], $value);
        }

        return $this->purifier->purify($value);
    }
}
