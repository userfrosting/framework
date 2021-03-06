<?php

/*
 * UserFrosting Framework (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/framework
 * @copyright Copyright (c) 2013-2021 Alexander Weissman, Louis Charette, Jordan Mele
 * @license   https://github.com/userfrosting/framework/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Fortress;

use UserFrosting\Fortress\RequestSchema\RequestSchemaInterface;
use UserFrosting\I18n\Translator;

/**
 * ServerSideValidator Interface.
 *
 * Loads validation rules from a schema and validates a target array of data.
 *
 * @author Alexander Weissman (https://alexanderweissman.com)
 */
interface ServerSideValidatorInterface
{
    /**
     * Set the schema for this validator, as a valid RequestSchemaInterface object.
     *
     * @param RequestSchemaInterface $schema A RequestSchemaInterface object, containing the validation rules.
     */
    public function setSchema(RequestSchemaInterface $schema);

    /**
     * Set the translator for this validator, as a valid Translator object.
     *
     * @param Translator $translator A Translator to be used to translate message ids found in the schema.
     */
    public function setTranslator(Translator $translator);

    /**
     * Validate the specified data against the schema rules.
     *
     * @param array $data An array of data, mapping field names to field values.
     *
     * @return bool True if the data was successfully validated, false otherwise.
     */
    public function validate(array $data);

    /**
     *  Get array of fields and data.
     *
     * @return array
     */
    public function data();

    /**
     * Get array of error messages.
     *
     * @return array|bool
     */
    public function errors();
}
