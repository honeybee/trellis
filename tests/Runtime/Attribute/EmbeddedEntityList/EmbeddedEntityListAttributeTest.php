<?php

namespace Trellis\Tests\Runtime\Attribute\EmbeddedEntityList;

use Mockery;
use Trellis\Runtime\Attribute\EmbeddedEntityList\EmbeddedEntityListAttribute;
use Trellis\Runtime\Attribute\EmbeddedEntityList\EmbeddedEntityListValueHolder;
use Trellis\Runtime\EntityTypeInterface;
use Trellis\Runtime\Validator\Result\IncidentInterface;
use Trellis\Tests\Runtime\Fixtures\ArticleType;
use Trellis\Tests\Runtime\Fixtures\Paragraph;
use Trellis\Tests\Runtime\Fixtures\ParagraphType;
use Trellis\Tests\Runtime\Fixtures\WorkflowStateType;
use Trellis\Tests\TestCase;

class EmbeddedEntityListAttributeTest extends TestCase
{
    const ATTR_NAME = 'content_objects';

    public function testCreate()
    {
        $embed_attribute = new EmbeddedEntityListAttribute(
            self::ATTR_NAME,
            $this->getTypeMock(),
            [ EmbeddedEntityListAttribute::OPTION_ENTITY_TYPES => [ ParagraphType::CLASS ] ]
        );
        $this->assertEquals($embed_attribute->getName(), self::ATTR_NAME);
    }

    public function testParentGetter()
    {
        $embed_attribute = new EmbeddedEntityListAttribute(
            self::ATTR_NAME,
            $this->getTypeMock(),
            [ EmbeddedEntityListAttribute::OPTION_ENTITY_TYPES => [ ParagraphType::CLASS ] ]
        );
        $paragraph_type = $embed_attribute->getEmbeddedTypeByPrefix('paragraph');
        $title_attribute = $paragraph_type->getAttribute('title');

        $this->assertEquals($embed_attribute->getName(), $title_attribute->getParent()->getName());
    }

    public function testParentEntityAccess()
    {
        $article_type = $this->getTypeMock();
        $article = $article_type->createEntity(
            [
                'uuid' => '7e185d43-f870-46e7-9cea-59800555e970',
                'content_objects' => [
                    [
                        '@type' => 'paragraph',
                        'title' => 'Foobar',
                        'content' => 'The quick brown bar fooed over the lazy snafu.'
                    ]
                ]
            ]
        );

        $paragraph = $article->getValue('content_objects')->getFirst();

        $this->assertInstanceOf(Paragraph::CLASS, $paragraph);
        $this->assertEquals($paragraph->getType()->getName(), 'Paragraph');
        $this->assertEquals($paragraph->getParent(), $article);
        $this->assertEquals($paragraph->getParent()->getType()->getName(), 'Article');
    }

    /**
     * @dataProvider getOptionsFixture
     */
    public function testCreateWithOptions(array $options)
    {
        $options = array_merge(
            [ EmbeddedEntityListAttribute::OPTION_ENTITY_TYPES => [ ParagraphType::CLASS ]],
            $options
        );

        $embed_attribute = new EmbeddedEntityListAttribute(self::ATTR_NAME, $this->getTypeMock(), $options);

        $this->assertEquals($embed_attribute->getName(), self::ATTR_NAME);
        $this->assertFalse($embed_attribute->hasOption('snafu_flag'));
        foreach ($options as $optName => $optValue) {
            $this->assertTrue($embed_attribute->hasOption($optName));
            $this->assertEquals($embed_attribute->getOption($optName), $optValue);
        }
    }

    /**
     * @dataProvider getEmbedFixture
     */
    public function testCreateValue(array $embed_data)
    {
        $embed_attribute = new EmbeddedEntityListAttribute(
            self::ATTR_NAME,
            $this->getTypeMock(),
            [ EmbeddedEntityListAttribute::OPTION_ENTITY_TYPES => [ ParagraphType::CLASS ] ]
        );

        $value = $embed_attribute->createValueHolder();
        $this->assertInstanceOf(EmbeddedEntityListValueHolder::CLASS, $value);

        $value->setValue($embed_data);
        $entity = $value->getValue()->getFirst();
        $this->assertInstanceOf(Paragraph::CLASS, $entity);

        foreach ($embed_data[0] as $attribute_name => $value) {
            if ($attribute_name === '@type') {
                $this->assertEquals($value, $entity->getType()->getPrefix());
            } else {
                $this->assertEquals($value, $entity->getValue($attribute_name));
            }
        }
    }

    public function testCreateWithMinCount()
    {
        $options = array_merge(
            [
                EmbeddedEntityListAttribute::OPTION_ENTITY_TYPES => [ ParagraphType::CLASS ],
                EmbeddedEntityListAttribute::OPTION_MIN_COUNT => 3
            ]
        );

        $embed_attribute = new EmbeddedEntityListAttribute(self::ATTR_NAME, $this->getTypeMock(), $options);
        $valueholder = $embed_attribute->createValueHolder();
        $result = $valueholder->setValue([]);
        $incident_name = $result->getViolatedRules()->getFirst()->getIncidents()->getItem('min_count')->getName();
        $this->assertEquals($embed_attribute->getDefaultValue()->isEmpty(), $valueholder->getValue()->isEmpty());
        $this->assertEquals($embed_attribute->getNullValue()->isEmpty(), $valueholder->getValue()->isEmpty());
        $this->assertEquals(IncidentInterface::ERROR, $result->getSeverity());
        $this->assertEquals('min_count', $incident_name);
    }

    public function testCreateWithMaxCount()
    {
        $options = array_merge(
            [
                EmbeddedEntityListAttribute::OPTION_ENTITY_TYPES => [ ParagraphType::CLASS ],
                EmbeddedEntityListAttribute::OPTION_MAX_COUNT => 1
            ]
        );

        $embed_attribute = new EmbeddedEntityListAttribute(self::ATTR_NAME, $this->getTypeMock(), $options);
        $valueholder = $embed_attribute->createValueHolder();
        $data = [
            [
                '@type' => 'paragraph',
                'title' => 'Foobar',
                'content' => 'The quick brown bar fooed over the lazy snafu.'
            ],
            [
                '@type' => 'paragraph',
                'title' => 'Foobar2',
                'content' => 'The quick lazy snafu fooed over the brown bar.'
            ]
        ];
        $result = $valueholder->setValue($data);
        $incident_name = $result->getViolatedRules()->getFirst()->getIncidents()->getItem('max_count')->getName();
        $this->assertEquals($embed_attribute->getDefaultValue()->isEmpty(), $valueholder->getValue()->isEmpty());
        $this->assertEquals($embed_attribute->getNullValue()->isEmpty(), $valueholder->getValue()->isEmpty());
        $this->assertEquals(IncidentInterface::ERROR, $result->getSeverity());
        $this->assertEquals('max_count', $incident_name);
    }

    public function testGetEmbedByPrefix()
    {
        $embed_attribute = new EmbeddedEntityListAttribute(
            self::ATTR_NAME,
            $this->getTypeMock(),
            [ EmbeddedEntityListAttribute::OPTION_ENTITY_TYPES => [ WorkflowStateType::CLASS ] ]
        );
        $workflow_state_type = $embed_attribute->getEmbeddedTypeByPrefix('workflow_state');
        $this->assertInstanceOf(WorkflowStateType::CLASS, $workflow_state_type);
    }

    /**
     * @codeCoverageIgnore
     */
    public static function getOptionsFixture()
    {
        // @todo generate random options.
        $fixtures = [];

        return [
            [
                [
                    'some_option_name' => 'some_option_value',
                    'another_option_name' => 'another_option_value'
                ],
                [
                    'some_option_name' => 23,
                    'another_option_name' => 5
                ],
                [
                    'some_option_name' => [ 'foo' => 'bar' ]
                ]
            ]
        ];

        return $fixtures;
    }

    /**
     * @codeCoverageIgnore
     */
    public static function getEmbedFixture()
    {
        // @todo generate random (utf-8) text
        $fixtures = [];

        $fixtures[] = [
            [
                [
                    'title' => 'This is a paragraph test title.',
                    'content' => 'And this is some paragraph test content.',
                    '@type' => 'paragraph'
                ]
            ]
        ];

        return $fixtures;
    }

    protected function getTypeMock($type_name = 'GenericMockType')
    {
        return new ArticleType();
    }
}
