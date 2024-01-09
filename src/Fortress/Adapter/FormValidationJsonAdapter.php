<?php

declare(strict_types=1);

/*
 * UserFrosting Framework (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/framework
 * @copyright Copyright (c) 2013-2021 Alexander Weissman, Louis Charette, Jordan Mele
 * @license   https://github.com/userfrosting/framework/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Fortress\Adapter;

/**
 * Loads validation rules from a schema and generates client-side rules
 * compatible with the FormValidation (http://formvalidation.io) JS plugin.
 *
 * Returns the rules as a JSON encoded string.
 */
final class FormValidationJsonAdapter implements ValidationAdapterInterface
{
    use FromSchemaTrait;

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function rules(): string
    {
        $arrayAdapter = new FormValidationArrayAdapter($this->schema, $this->translator);

        return json_encode($arrayAdapter->rules(), JSON_PRETTY_PRINT | JSON_FORCE_OBJECT | JSON_THROW_ON_ERROR);
    }
}
