<?php

namespace Trellis\Common;

use ReflectionClass;
use Traversable;

class Object implements ObjectInterface
{
    const OBJECT_TYPE = '@type';

    const ANNOTATION_HIDDEN_PROPERTY = 'hiddenProperty';

    /**
     * @hiddenProperty
     */
    protected $hidden_properties_;

    /**
     * Creates a new object instance that has the given data set on it's properties.
     *
     * @param array $state data to set on the object (key-value pairs)
     */
    public function __construct(array $state = [])
    {
        foreach ($state as $property_name => $property_value) {
            $tmp_property_name = ucwords(str_replace(['-', '_'], ' ', $property_name));
            $studly_property_name = str_replace(' ', '', $tmp_property_name);
            //$camelcased_property = lcfirst($studly_property_name);

            $setter_method = 'set' . ucfirst($studly_property_name);
            if (method_exists($this, $setter_method)) {
                $this->$setter_method($property_value);
            } elseif (property_exists($this, $property_name)) {
                $this->$property_name = $property_value;
            }
        }
    }

    /**
     * Return an array representation of the current object.
     * The array will contain the object's property names as keys
     * and the property values as array values.
     * Nested 'ObjectInterface' and 'Options' instances will also be turned into arrays.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->extractData($this);
    }

    protected function extractData(ObjectInterface $object)
    {
        $data = [ self::OBJECT_TYPE => get_class($object) ];
        $hidden_properties = $object->getHiddenProperties();

        foreach (get_object_vars($object) as $prop => $value) {
            if (in_array($prop, $hidden_properties)) {
                continue;
            }

            $data[$prop] = $value;
            if ($value instanceof ObjectInterface) {
                $data[$prop] = $value->toArray();
            } elseif (is_array($value) || $value instanceof Traversable) {
                foreach ($value as $nested_prop => $nested_value) {
                    if ($nested_value instanceof ObjectInterface) {
                        $data[$prop][$nested_prop] = $this->extractData($nested_value);
                    }
                }
            }
        }

        return $data;
    }

    public function createCopyWith(array $new_state)
    {
        return new static(array_merge($this->toArray(), $new_state));
    }

    protected function getHiddenProperties()
    {
        if (!$this->hidden_properties_) {
            $this->hidden_properties_ = [];
            $class = new ReflectionClass($this);
            foreach ($class->getProperties() as $property) {
                $annotations = $this->parseDocBlockAnnotations(
                    $property->getDocComment()
                );

                if (in_array(self::ANNOTATION_HIDDEN_PROPERTY, $annotations)) {
                    $this->hidden_properties_[] = $property->getName();
                }
            }
        }

        return $this->hidden_properties_;
    }

    protected function parseDocBlockAnnotations($doc_block)
    {
        $annotation_pattern = '~\*\s+@(?<property>\w+)~';

        preg_match_all($annotation_pattern, $doc_block, $matches);

        return $matches['property'];
    }
}
