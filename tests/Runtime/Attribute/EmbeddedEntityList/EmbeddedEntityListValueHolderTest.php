<?php

namespace Trellis\Tests\Runtime\Attribute\EmbeddedEntityList;

use Trellis\Runtime\Attribute\EmbeddedEntityList\EmbeddedEntityListAttribute;
use Trellis\Runtime\Attribute\EmbeddedEntityList\EmbeddedEntityListValueHolder;
use Trellis\Runtime\Entity\EntityList;
use Trellis\Runtime\ValueHolder\ValueChangedEvent;
use Trellis\Runtime\ValueHolder\ValueChangedListenerInterface;
use Trellis\Tests\Runtime\Fixtures\ParagraphType;
use Trellis\Tests\TestCase;
use Trellis\Tests\Runtime\Fixtures\ArticleType;
use Mockery;

class EmbeddedEntityListValueHolderTest extends TestCase
{
    const ATTR_NAME = 'content_objects';

    public function testCreate()
    {
        $value = new EmbeddedEntityListValueHolder(
            new EmbeddedEntityListAttribute(
                self::ATTR_NAME,
                $this->getTypeMock(),
                [ EmbeddedEntityListAttribute::OPTION_ENTITY_TYPES => [ ParagraphType::CLASS ] ]
            )
        );

        $this->assertInstanceOf(EmbeddedEntityListValueHolder::CLASS, $value);
    }

    public function testDefaultValue()
    {
        $embed_attribute = new EmbeddedEntityListAttribute(
            self::ATTR_NAME,
            $this->getTypeMock(),
            [ EmbeddedEntityListAttribute::OPTION_ENTITY_TYPES => [ ParagraphType::CLASS ] ]
        );

        $value = $embed_attribute->createValueHolder();

        $entity_list = $value->getValue();
        $this->assertInstanceOf(EntityList::CLASS, $entity_list);
        $this->assertEquals(0, $entity_list->getSize());
    }

    public function testValueChangedEvents()
    {
        $listener = Mockery::mock(ValueChangedListenerInterface::CLASS);
        $listener->shouldReceive('onValueChanged')->with(ValueChangedEvent::CLASS)->twice();

        $article_type = $this->getTypeMock();
        $embed_type = new ParagraphType($article_type, $article_type->getAttribute('content_objects'));
        $embedd_entity = $embed_type->createEntity(
            [ 'title' => 'Hello world', 'content' => 'Foobar lorem ipsum...' ]
        );

        $embed_attribute = new EmbeddedEntityListAttribute(
            self::ATTR_NAME,
            $this->getTypeMock(),
            [ EmbeddedEntityListAttribute::OPTION_ENTITY_TYPES => [ ParagraphType::CLASS ] ]
        );

        $value = $embed_attribute->createValueHolder();
        $value->addValueChangedListener($listener);

        $entity_list = $value->getValue();
        $entity_list->push($embedd_entity);

        $embedd_entity->setValue('title', 'Kthxbye');

        $this->assertInstanceOf(EntityList::CLASS, $entity_list);
        $this->assertEquals(1, $entity_list->getSize());
    }

    protected function getTypeMock($type_name = 'GenericMockType')
    {
        return new ArticleType();
    }
}
