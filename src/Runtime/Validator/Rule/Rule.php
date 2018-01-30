<?php

namespace Trellis\Runtime\Validator\Rule;

use ReflectionClass;
use Trellis\Common\BaseObject;
use Trellis\Runtime\Entity\EntityInterface;
use Trellis\Runtime\Validator\Result\Incident;
use Trellis\Runtime\Validator\Result\IncidentMap;

abstract class Rule extends BaseObject implements RuleInterface
{
    private $name;

    private $options;

    private $incidents;

    private $sanitized_value;

    /**
     * Validates the given value and should set a sanitized value via
     * ```$this->setSanitizedValue($sanitized_value);``` as a side effect.
     *
     * The method should not mutate the given value!
     *
     * The sanitized value will be given to the next validation rules by
     * the validator and will end up being used as the new value (if valid).
     *
     * @param mixed $value the valueholder's value to validate
     *
     * @return boolean true if valid; false otherwise.
     */
    abstract protected function execute($value, EntityInterface $entity = null);

    public function __construct($name, array $options = [])
    {
        $this->name = $name;
        $this->options = $options;
        $this->incidents = new IncidentMap();
    }

    public function apply($value, EntityInterface $entity = null)
    {
        $this->incidents = new IncidentMap(); // TODO does this have to be a map? accumulated throwError() in foreach?
        $this->sanitized_value = null;

        if (true === ($success = $this->execute($value, $entity))) {
            // TODO this actually prevents setting sanitized_value explicitely to NULL; override this method if needed
            $this->sanitized_value = ($this->sanitized_value === null) ? $value : $this->sanitized_value;
        }

        return $success;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function getOption($name, $default = null)
    {
        if ($this->hasOption($name)) {
            return $this->options[$name];
        } else {
            return $default;
        }
    }

    public function hasOption($name)
    {
        return array_key_exists($name, $this->options);
    }

    public function getIncidents()
    {
        return $this->incidents;
    }

    public function getSanitizedValue()
    {
        return $this->sanitized_value;
    }

    protected function setSanitizedValue($sanitized_value)
    {
        $this->sanitized_value = $sanitized_value;
    }

    protected function throwIncidentsAsErrors($rule, $property = null, array $parameters = [])
    {
        if ($property && !isset($parameters['path_parts'])) {
            $parameters['path_parts'] = [ $property ];
        }
        foreach ($rule->getIncidents() as $incident) {
            $name = $incident->getName();
            $params = $parameters;
            $this->throwError(
                is_string($property) && !empty($property) ? $property . '.' . $name : $name,
                array_merge_recursive($incident->getParameters(), $parameters),
                $incident->getSeverity()
            );
        }
    }

    protected function throwError($name, array $parameters = [], $severity = Incident::ERROR)
    {
        $this->incidents->setItem($name, new Incident($name, $parameters, $severity));
    }

    protected function toBoolean($value)
    {
        $bool = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        if (null === $bool) {
            return false;
        }

        return $bool;
    }

    /**
     * @return bool true if argument is an associative array. False otherwise.
     */
    protected function isAssoc(array $array)
    {
        foreach (array_keys($array) as $key => $value) {
            if ($key !== $value) {
                return true;
            }
        }

        return false;
    }

    protected function getSupportedOptionsFor($rule, $property_name)
    {
        $options = $this->getOptions();
        $prefix = $property_name . '_';
        $rule_options = [];

        // map all options of this rule for the given property to
        // the unprefixed normal options the given rule supports
        foreach ($rule::getOptionConstantValues() as $option_name) {
            if (array_key_exists($prefix . $option_name, $options)) {
                $rule_options[$option_name] = $options[$prefix . $option_name];
            }
        }

        return $rule_options;
    }

    public static function getOptionConstantValues($prefix = '')
    {
        $class = new ReflectionClass(get_called_class());
        $constants = $class->getConstants();
        $supported_options = [];

        foreach ($constants as $key => $name) {
            if (substr($key, 0, 7) === 'OPTION_') {
                $supported_options[] = $prefix . $name;
            }
        }

        return $supported_options;
    }
}
