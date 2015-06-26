<?php

namespace Trellis\CodeGen\Schema;

use Trellis\Common\Object;

class EntityTypeDefinition extends Object
{
    protected $name;

    protected $implementor;

    protected $entity_implementor;

    protected $description;

    protected $options;

    protected $attributes;

    public function __construct(array $state = [])
    {
        $this->attributes = new AttributeDefinitionList();
        $this->options = new OptionDefinitionList();

        parent::__construct($state);
    }

    public function getName()
    {
        return $this->name;
    }

    public function getImplementor()
    {
        return $this->implementor;
    }

    public function getEntityImplementor()
    {
        return $this->entity_implementor;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }
}
