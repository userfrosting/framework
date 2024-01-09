<?php

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
 * @deprecated Since 5.1 Instead use :
 *  - json, $stringEncode = true : JqueryValidationJsonAdapter
 *  - json, $stringEncode = false : JqueryValidationArrayAdapter
 */
class JqueryValidationAdapter implements ValidationAdapterInterface
{
    use FromSchemaTrait;

    /**
     * Generate jQuery Validation compatible rules from the specified
     * RequestSchema, as a JSON document. See url below as an example of what
     * this function will generate.
     *
     * @see https://github.com/jzaefferer/jquery-validation/blob/master/demo/bootstrap/index.html#L168-L209
     *
     * @param string $format       (Default: json)
     * @param bool   $stringEncode Specify whether to return a PHP array, or a JSON-encoded string. (default: false)
     * @param string $arrayPrefix  (Default: '')
     *
     * @return string|mixed[] Returns either the array of rules, or a JSON-encoded representation of that array.
     */
    public function rules(string $format = 'json', bool $stringEncode = false, string $arrayPrefix = ''): string|array
    {
        return match ($stringEncode) {
            true  => (new JqueryValidationJsonAdapter($this->schema, $this->translator))->rules($arrayPrefix),
            false => (new JqueryValidationArrayAdapter($this->schema, $this->translator))->rules($arrayPrefix),
        };
    }
}
