<?php

/*
 * UserFrosting Framework (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/framework
 * @copyright Copyright (c) 2013-2021 Alexander Weissman, Louis Charette, Jordan Mele
 * @license   https://github.com/userfrosting/framework/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Fortress;

use UnhandledMatchError;
use UserFrosting\Fortress\RequestSchema\RequestSchemaInterface;
use UserFrosting\I18n\Translator;
use Valitron\Validator;

/**
 * Loads validation rules from a schema and validates a target array of data.
 */
class ServerSideValidator extends Validator implements ServerSideValidatorInterface
{
    /**
     * Create a new server-side validator.
     *
     * @param RequestSchemaInterface $schema     A RequestSchemaInterface object, containing the validation rules.
     * @param Translator             $translator A Translator to be used to translate message ids found in the schema.
     */
    public function __construct(protected RequestSchemaInterface $schema, protected  Translator $translator)
    {
        // Construct the parent with an empty data array.
        // TODO: use locale of translator to determine Valitron language?
        parent::__construct([]);
    }

    /**
     * {@inheritdoc}
     */
    public function setSchema(RequestSchemaInterface $schema): void
    {
        $this->schema = $schema;
    }

    /**
     * {@inheritdoc}
     */
    public function setTranslator(Translator $translator): void
    {
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(array $data = []): bool
    {
        $this->_fields = $data;         // Setting the parent class Validator's field data.
        $this->generateSchemaRules();   // Build Validator rules from the schema.

        return parent::validate();      // Validate!
    }

    /**
     * {@inheritdoc}
     *
     * We expose this method to the public interface for testing purposes.
     */
    public function hasRule($name, $field)
    {
        return parent::hasRule($name, $field);
    }

    /**
     * Validate that a field has a particular value.
     *
     * @param string               $field
     * @param mixed                $value
     * @param array{string, mixed} $params
     *
     * @return bool
     */
    protected function validateEqualsValue(string $field, mixed $value, array $params): bool
    {
        $targetValue = $params[0];
        $caseSensitive = is_bool($params[1]) ? $params[1] : false;

        if (!$caseSensitive) {
            $value = strtolower($value);
            $targetValue = strtolower($targetValue);
        }

        return $value == $targetValue;
    }

    /**
     * Validate that a field does NOT have a particular value.
     *
     * @param string               $field
     * @param mixed                $value
     * @param array{string, mixed} $params
     *
     * @return bool
     */
    protected function validateNotEqualsValue(string $field, mixed $value, array $params): bool
    {
        return !$this->validateEqualsValue($field, $value, $params);
    }

    /**
     * Matches US phone number format
     * Ported from jqueryValidation rules.
     *
     * where the area code may not start with 1 and the prefix may not start with 1
     * allows '-' or ' ' as a separator and allows parens around area code
     * some people may want to put a '1' in front of their number
     *
     * 1(212)-999-2345 or
     * 212 999 2344 or
     * 212-999-0983
     *
     * but not
     * 111-123-5434
     * and not
     * 212 123 4567
     *
     * @param string $field
     * @param mixed  $value
     *
     * @return bool
     */
    protected function validatePhoneUS(string $field, mixed $value): bool
    {
        $value = preg_replace('/\s+/', '', $value);

        return (strlen($value) > 9) &&
            preg_match('/^(\+?1-?)?(\([2-9]([02-9]\d|1[02-9])\)|[2-9]([02-9]\d|1[02-9]))-?[2-9]([02-9]\d|1[02-9])-?\d{4}$/', $value) === 1;
    }

    /**
     * Validate that a field contains only valid username characters: alpha-numeric characters, dots, dashes, and underscores.
     *
     * @param string $field
     * @param mixed  $value
     *
     * @return bool
     */
    protected function validateUsername(string $field, mixed $value): bool
    {
        return preg_match('/^([a-z0-9\.\-_])+$/i', $value) === 1;
    }

    /**
     * Add a rule to the validator, along with a specified error message if that rule is failed by the data.
     *
     * @param string      $rule       The name of the validation rule.
     * @param string|null $messageSet The message to display when validation against this rule fails.
     *
     * @return string
     */
    protected function ruleWithMessage(string $rule, ?string $messageSet): string
    {
        // Weird way to adapt with Valitron's funky interface
        $params = array_merge([$rule], array_slice(func_get_args(), 2));
        call_user_func_array([$this, 'rule'], $params);

        // Set message.  Use Valitron's default message if not specified in the schema.
        if ($messageSet === null) {
            $message = static::$_ruleMessages[$rule] ?? '';
            $message = vsprintf($message, array_slice(func_get_args(), 3));
            $messageSet = "'{$params[1]}' $message";
        }

        return $messageSet;
    }

    /**
     * Generate and add rules from the schema.
     */
    protected function generateSchemaRules(): void
    {
        foreach ($this->schema->all() as $fieldName => $field) {
            if (!isset($field['validators'])) {
                continue;
            }

            $validators = $field['validators'];
            foreach ($validators as $validatorName => $validator) {
                // Skip messages that are for client-side use only
                if (isset($validator['domain']) && $validator['domain'] == 'client') {
                    continue;
                }

                // Generate translated message
                if (isset($validator['message'])) {
                    $params = array_merge(['self' => $fieldName], $validator);
                    $messageSet = $this->translator->translate($validator['message'], $params);
                } else {
                    $messageSet = null;
                }

                try {
                    $messageSet = match ($validatorName) {
                        'array'                  => $this->ruleWithMessage('array', $messageSet, $fieldName), // For now, just check that it is an array.  Really we need a new validation rule here.
                        'email'                  => $this->ruleWithMessage('email', $messageSet, $fieldName),
                        'equals'                 => $this->ruleWithMessage('equalsValue', $messageSet, $fieldName, $validator['value'], $validator['caseSensitive']),
                        'integer'                => $this->ruleWithMessage('integer', $messageSet, $fieldName),
                        'matches'                => $this->ruleWithMessage('equals', $messageSet, $fieldName, $validator['field']),
                        'member_of'              => $this->ruleWithMessage('in', $messageSet, $fieldName, $validator['values'], true),    // Strict comparison
                        'no_leading_whitespace'  => $this->ruleWithMessage('regex', $messageSet, $fieldName, "/^\S.*$/"),
                        'no_trailing_whitespace' => $this->ruleWithMessage('regex', $messageSet, $fieldName, "/^.*\S$/"),
                        'not_equals'             => $this->ruleWithMessage('notEqualsValue', $messageSet, $fieldName, $validator['value'], $validator['caseSensitive']),
                        'not_matches'            => $this->ruleWithMessage('different', $messageSet, $fieldName, $validator['field']),
                        'not_member_of'          => $this->ruleWithMessage('notIn', $messageSet, $fieldName, $validator['values'], true),  // Strict comparison
                        'numeric'                => $this->ruleWithMessage('numeric', $messageSet, $fieldName),
                        'regex'                  => $this->ruleWithMessage('regex', $messageSet, $fieldName, '/' . $validator['regex'] . '/'),
                        'required'               => $this->ruleWithMessage('required', $messageSet, $fieldName),
                        'telephone'              => $this->ruleWithMessage('phoneUS', $messageSet, $fieldName),
                        'uri'                    => $this->ruleWithMessage('url', $messageSet, $fieldName),
                        'username'               => $this->ruleWithMessage('username', $messageSet, $fieldName),
                        'range'                  => call_user_func(function () use ($validator, $messageSet, $fieldName) {
                            $set = [];
                            if (isset($validator['min'])) {
                                $set[] = $this->ruleWithMessage('min', $messageSet, $fieldName, $validator['min']);
                            }
                            if (isset($validator['max'])) {
                                $set[] = $this->ruleWithMessage('max', $messageSet, $fieldName, $validator['max']);
                            }

                            return $set;
                        }),
                        'length' => call_user_func(function () use ($validator, $messageSet, $fieldName) {
                            $set = [];
                            if (isset($validator['min']) && isset($validator['max'])) {
                                $set[] = $this->ruleWithMessage('lengthBetween', $messageSet, $fieldName, $validator['min'], $validator['max']);
                            } else {
                                if (isset($validator['min'])) {
                                    $set[] = $this->ruleWithMessage('lengthMin', $messageSet, $fieldName, $validator['min']);
                                }
                                if (isset($validator['max'])) {
                                    $set[] = $this->ruleWithMessage('lengthMax', $messageSet, $fieldName, $validator['max']);
                                }
                            }

                            return $set;
                        }),
                    };
                } catch (UnhandledMatchError $e) {
                    continue;
                }

                // Add new message for each message set
                $messageSet = is_array($messageSet) ? $messageSet : [$messageSet];
                foreach ($messageSet as $message) {
                    $this->message($message);
                }
            }
        }
    }
}
