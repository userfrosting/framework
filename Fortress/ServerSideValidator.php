<?php

/**
 * ServerSideValidator Class
 *
 * Loads validation rules from a schema and validates a target array of data.
 *
 * @package userfrosting/fortress
 * @author Alex Weissman
 * @link https://alexanderweissman.com
 * @license MIT
 */
namespace UserFrosting\Fortress;

use \Valitron\Validator;

class ServerSideValidator extends Validator implements ServerSideValidatorInterface
{
    /**
     * @var RequestSchema
     */
    protected $schema;

    /**
     * @var MessageTranslatorInterface
     */
    protected $translator;

    /** Create a new server-side validator.
     *
     * @param RequestSchema $schema A RequestSchema object, containing the validation rules.
     * @param MessageTranslator $translator A MessageTranslator to be used to translate message ids found in the schema.
     */
    public function __construct($schema, $translator)
    {
        // Set schema
        $this->setSchema($schema);

        // Set translator
        $this->setTranslator($translator);
        // TODO: use locale of translator to determine Valitron language?

        // Construct the parent with an empty data array.
        parent::__construct([]);
    }

    /**
     * Set the schema for this validator, as a valid RequestSchema object.
     *
     * @param RequestSchema $schema A RequestSchema object, containing the validation rules.
     */
    public function setSchema($schema)
    {
        $this->schema = $schema;
    }

    /**
     * Set the translator for this validator, as a valid MessageTranslator object.
     *
     * @param MessageTranslator $translator A MessageTranslator to be used to translate message ids found in the schema.
     */
    public function setTranslator($translator)
    {
        $this->translator = $translator;
    }

    /**
     * Validate the specified data against the schema rules.
     *
     * @param array $data An array of data, mapping field names to field values.
     * @return boolean True if the data was successfully validated, false otherwise.
     */
    public function validate($data = [])
    {
        $this->_fields = $data;         // Setting the parent class Validator's field data.
        $this->generateSchemaRules();   // Build Validator rules from the schema.
        return parent::validate();      // Validate!
    }

    /**
     * Validate that a field has a particular value.
     *
     * @param  string $field
     * @param  mixed  $value
     * @param  string $targetValue
     * @param  bool $caseSensitive
     * @return bool
     */
    protected function validateEqualsValue($field, $value, $targetValue, $caseSensitive = false)
    {
        if (!$caseSensitive) {
            $value = strtolower($value);
            $targetValue = strtolower($targetValue);
        }
        return $value == $targetValue;
    }

    /**
     * Validate that a field does NOT have a particular value.
     *
     * @param  string $field
     * @param  mixed  $value
     * @param  string $targetValue
     * @param  bool $caseSensitive
     * @return bool
     */
    protected function validateNotEqualsValue($field, $value, $targetValue, $caseSensitive = false)
    {
        return !$this->validateEqualsValue($field, $value, $targetValue, $caseSensitive);
    }

    /**
     * Matches US phone number format
     * Ported from jqueryValidation rules
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
     * @param  string $field
     * @param  mixed  $value
     * @return bool
     */
    protected function validatePhoneUS($field, $value)
    {
        $value = preg_replace('/\s+/', '', $value);
        return (strlen($value) > 9) &&
            preg_match('/^(\+?1-?)?(\([2-9]([02-9]\d|1[02-9])\)|[2-9]([02-9]\d|1[02-9]))-?[2-9]([02-9]\d|1[02-9])-?\d{4}$/', $value);
    }

    /**
     * Validate that a field contains only valid username characters: alpha-numeric characters, dots, dashes, and underscores
     *
     * @param  string $field
     * @param  mixed  $value
     * @return bool
     */
    protected function validateUsername($field, $value)
    {
        return preg_match('/^([a-z0-9\.\-_])+$/i', $value);
    }

    /**
     * Add a rule to the validator, along with a specified error message if that rule is failed by the data.
     *
     * @param string $rule The name of the validation rule.
     * @param string $messageSet The message to display when validation against this rule fails.
     */
    private function ruleWithMessage($rule, $messageSet)
    {
        // Weird way to adapt with Valitron's funky interface
        $params = array_merge([$rule], array_slice(func_get_args(), 2));
        call_user_func_array([$this,"rule"], $params);
        // Set message.  Use Valitron's default message if not specified in the schema.
        if (!$messageSet) {
            $messageSet = "'" . $params[1] . "' " . vsprintf(static::$_ruleMessages[$rule], array_slice(func_get_args(), 3));
        }
        $this->message($messageSet);
    }

    /**
     * Generate and add rules from the schema.
     */
    private function generateSchemaRules()
    {
        foreach ($this->schema->getSchema() as $fieldName => $field) {
            if (!isset($field['validators'])) {
                continue;
            }

            $validators = $field['validators'];
            foreach ($validators as $validatorName => $validator) {
                // Skip messages that are for client-side use only
                if (isset($validator['domain']) && $validator['domain'] == "client") {
                    continue;
                }

                // Generate translated message
                if (isset($validator['message'])) {
                    $params = array_merge(["self" => $fieldName], $validator);
                    $messageSet = $this->translator->translate($validator['message'], $params);
                } else {
                    $messageSet = null;
                }

                // Array validator
                if ($validatorName == "array") {
                    // For now, just check that it is an array.  Really we need a new validation rule here.
                    $this->ruleWithMessage("array", $messageSet, $fieldName);
                }
                // Email validator
                if ($validatorName == "email") {
                    $this->ruleWithMessage("email", $messageSet, $fieldName);
                }
                // Equals validator
                if ($validatorName == "equals") {
                    $this->ruleWithMessage("equalsValue", $messageSet, $fieldName, $validator['value'], $validator['caseSensitive']);
                }
                // Integer validator
                if ($validatorName == "integer") {
                    $this->ruleWithMessage("integer", $messageSet, $fieldName);
                }
                // String length validator
                if ($validatorName == "length") {
                    if (isset($validator['min']) && isset($validator['max'])) {
                        $this->ruleWithMessage("lengthBetween", $messageSet, $fieldName, $validator['min'], $validator['max']);
                    } else {
                        if (isset($validator['min'])) {
                            $this->ruleWithMessage("lengthMin", $messageSet, $fieldName, $validator['min']);
                        }
                        if (isset($validator['max'])) {
                            $this->ruleWithMessage("lengthMax", $messageSet, $fieldName, $validator['max']);
                        }
                    }
                }
                // Match another field
                if ($validatorName == "matches") {
                    $this->ruleWithMessage("equals", $messageSet, $fieldName, $validator['field']);
                }
                // Check membership in array
                if ($validatorName == "member_of") {
                    $this->ruleWithMessage("in", $messageSet, $fieldName, $validator['values'], true);    // Strict comparison
                }
                // No leading whitespace
                if ($validatorName == "no_leading_whitespace") {
                    $this->ruleWithMessage("regex", $messageSet, $fieldName, "/^\S.*$/");
                }
                // No trailing whitespace
                if ($validatorName == "no_trailing_whitespace") {
                    $this->ruleWithMessage("regex", $messageSet, $fieldName, "/^.*\S$/");
                }
                // Negation of equals validator
                if ($validatorName == "not_equals") {
                    $this->ruleWithMessage("notEqualsValue", $messageSet, $fieldName, $validator['value'], $validator['caseSensitive']);
                }
                // Negation of match another field
                if ($validatorName == "not_matches") {
                    $this->ruleWithMessage("different", $messageSet, $fieldName, $validator['field']);
                }
                // Negation of membership
                if ($validatorName == "not_member_of") {
                    $this->ruleWithMessage("notIn", $messageSet, $fieldName, $validator['values'], true);  // Strict comparison
                }
                // Numeric validator
                if ($validatorName == "numeric") {
                    $this->ruleWithMessage("numeric", $messageSet, $fieldName);
                }
                // Numeric range validator
                if ($validatorName == "range") {
                    if (isset($validator['min'])) {
                        $this->ruleWithMessage("min", $messageSet, $fieldName, $validator['min']);
                    }
                    if (isset($validator['max'])) {
                        $this->ruleWithMessage("max", $messageSet, $fieldName, $validator['max']);
                    }
                }
                // Regex validator
                if ($validatorName == "regex") {
                    $this->ruleWithMessage("regex", $messageSet, $fieldName, "/" . $validator['regex'] . "/");
                }
                // Required validator
                if ($validatorName == "required") {
                    $this->ruleWithMessage("required", $messageSet, $fieldName);
                }
                // Phone validator
                if ($validatorName == "telephone") {
                    $this->ruleWithMessage("phoneUS", $messageSet, $fieldName);
                }
                // URI validator
                if ($validatorName == "uri") {
                    $this->ruleWithMessage("url", $messageSet, $fieldName);
                }
                // Username
                if ($validatorName == "username") {
                    $this->ruleWithMessage("username", $messageSet, $fieldName);
                }
            }
        }
    }
}
