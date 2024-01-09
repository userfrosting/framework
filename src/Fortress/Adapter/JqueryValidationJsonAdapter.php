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
 * compatible with the jQuery Validation (http://http://jqueryvalidation.org)
 * JS plugin.
 *
 * Generate jQuery Validation compatible rules from the specified
 * RequestSchema, as a JSON document. See url below as an example of what
 * this function will generate.
 *
 * @see https://github.com/jzaefferer/jquery-validation/blob/master/demo/bootstrap/index.html#L168-L209
 *
 * Returns the rules as a JSON encoded string.
 */
final class JqueryValidationJsonAdapter implements ValidationAdapterInterface
{
    use FromSchemaTrait;

    /**
     * {@inheritdoc}
     * @param string $arrayPrefix (Default: '')
     *
     * @return string
     */
    public function rules(string $arrayPrefix = ''): string
    {
        $arrayAdapter = new JqueryValidationArrayAdapter($this->schema, $this->translator);

        return json_encode($arrayAdapter->rules($arrayPrefix), JSON_PRETTY_PRINT | JSON_FORCE_OBJECT | JSON_THROW_ON_ERROR);
    }
}
