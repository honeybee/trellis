<?php

namespace Trellis\Runtime\Validator\Rule;

use Trellis\Runtime\Entity\EntityInterface;
use Trellis\Runtime\Validator\Result\IncidentMap;

interface RuleInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @param mixed $value
     *
     * @return boolean
     */
    public function apply($value, EntityInterface $entity = null);

    /**
     * @param string $name
     * @param mixed $default
     *
     * @return mixed The option's value if the option is set, $default otherwise.
     */
    public function getOption($name, $default = null);

    /**
     * @return boolean
     */
    public function hasOption($name);

    /**
     * @return IncidentMap
     */
    public function getIncidents();

    /**
     * @return mixed
     */
    public function getSanitizedValue();
}
