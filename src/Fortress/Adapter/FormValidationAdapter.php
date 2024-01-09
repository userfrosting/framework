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
 * @deprecated Since 5.1 Instead use :
 *  - json, $stringEncode = true : FormValidationJsonAdapter
 *  - json, $stringEncode = false : FormValidationArrayAdapter
 *  - html5 : FormValidationHtml5Adapter
 */
class FormValidationAdapter implements ValidationAdapterInterface
{
    use FromSchemaTrait;

    /**
     * {@inheritdoc}
     *
     * @return mixed[]|string
     */
    public function rules(string $format = 'json', bool $stringEncode = true): array|string
    {
        return match (true) {
            ($format === 'html5')                  => (new FormValidationHtml5Adapter($this->schema, $this->translator))->rules(),
            ($format === 'json' && !$stringEncode) => (new FormValidationArrayAdapter($this->schema, $this->translator))->rules(),
            default                                => (new FormValidationJsonAdapter($this->schema, $this->translator))->rules(),
        };
    }
}
