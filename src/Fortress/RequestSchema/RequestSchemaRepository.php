<?php

/*
 * UserFrosting Framework (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/framework
 * @copyright Copyright (c) 2013-2021 Alexander Weissman, Louis Charette, Jordan Mele
 * @license   https://github.com/userfrosting/framework/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Fortress\RequestSchema;

use UserFrosting\Support\Repository\Repository;

/**
 * Represents a schema for an HTTP request, compliant with the WDVSS standard
 * (https://github.com/alexweissman/wdvss).
 */
class RequestSchemaRepository extends Repository implements RequestSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function setDefault(string $field, string $value): static
    {
        if (!isset($this->items[$field])) {
            $this->items[$field] = [];
        }

        $this->items[$field]['default'] = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addValidator(string $field, string $validatorName, array $parameters = []): static
    {
        if (!isset($this->items[$field])) {
            $this->items[$field] = [];
        }

        if (!isset($this->items[$field]['validators'])) {
            $this->items[$field]['validators'] = [];
        }

        $this->items[$field]['validators'][$validatorName] = $parameters;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeValidator(string $field, string $validatorName): static
    {
        unset($this->items[$field]['validators'][$validatorName]);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setTransformations(string $field, string|array $transformations = []): static
    {
        if (!is_array($transformations)) {
            $transformations = [$transformations];
        }

        if (!isset($this->items[$field])) {
            $this->items[$field] = [];
        }

        $this->items[$field]['transformations'] = $transformations;

        return $this;
    }
}
