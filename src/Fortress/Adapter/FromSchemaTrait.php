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

use UserFrosting\Fortress\RequestSchema\RequestSchemaInterface;
use UserFrosting\I18n\Translator;

/**
 * Helper trait for validation adapters. Adds schema and translator properties
 * and setters.
 */
trait FromSchemaTrait
{
    /**
     * @param RequestSchemaInterface $schema     A RequestSchema object, containing the validation rules.
     * @param Translator             $translator A Translator to be used to translate message ids found in the schema.
     */
    // TODO : make schema an argument for `rules()`, so it's easier to inject
    // an adapter with DI.
    // In any case, the deprecated method shouldn't use the trait.
    public function __construct(
        protected RequestSchemaInterface $schema,
        protected Translator $translator
    ) {
    }

    /**
     * Set the schema for this validator.
     *
     * @param RequestSchemaInterface $schema A RequestSchemaInterface object, containing the validation rules.
     *
     * @return $this
     *
     * @deprecated 5.1
     */
    public function setSchema(RequestSchemaInterface $schema): static
    {
        $this->schema = $schema;

        return $this;
    }

    /**
     * Set the translator for this validator, as a valid MessageTranslator object.
     *
     * @param Translator $translator A Translator to be used to translate message ids found in the schema.
     *
     * @return $this
     *
     * @deprecated 5.1
     */
    public function setTranslator(Translator $translator): static
    {
        $this->translator = $translator;

        return $this;
    }
}
