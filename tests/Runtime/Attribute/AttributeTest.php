<?php

namespace Trellis\Tests\Runtime\Attribute;

use Trellis\Tests\Runtime\Fixtures\ArticleType;
use Trellis\Tests\TestCase;

class AttributeTest extends TestCase
{
    /**
     * @dataProvider attributePathProvider
     */
    public function testGetPath($attribute, $expected_path)
    {
        $this->assertEquals($expected_path, $attribute->getPath());
    }

    /**
     * @dataProvider attributeRootTypeProvider
     */
    public function testGetRootType($attribute, $entity_type)
    {
        $this->assertEquals($entity_type->getName(), $attribute->getRootType()->getName());
    }

    public function attributePathProvider()
    {
        $article_type = new ArticleType();
        $headline_attribute = $article_type->getAttribute('headline');

        $content_objects_attribute = $article_type->getAttribute('content_objects');
        $paragraph_type = $content_objects_attribute->getEmbeddedTypeByPrefix('paragraph');
        $title_attribute = $paragraph_type->getAttribute('title');

        $workflow_state_attribute = $article_type->getAttribute('workflow_state');
        $workflow_state_type = $workflow_state_attribute->getEmbeddedTypeByPrefix('workflow_state');
        $workflow_step_attribute = $workflow_state_type->getAttribute('workflow_step');

        return [
            [ $headline_attribute, 'headline' ],
            [ $title_attribute, 'content_objects.paragraph.title' ],
            [ $workflow_step_attribute, 'workflow_state.workflow_state.workflow_step' ]
        ];
    }

    public function attributeRootTypeProvider()
    {
        $article_type = new ArticleType();
        $headline_attribute = $article_type->getAttribute('headline');

        $content_objects_attribute = $article_type->getAttribute('content_objects');
        $paragraph_type = $content_objects_attribute->getEmbeddedTypeByPrefix('paragraph');
        $title_attribute = $paragraph_type->getAttribute('title');

        $workflow_state_attribute = $article_type->getAttribute('workflow_state');
        $workflow_state_type = $workflow_state_attribute->getEmbeddedTypeByPrefix('workflow_state');
        $workflow_step_attribute = $workflow_state_type->getAttribute('workflow_step');

        return [
            [ $headline_attribute, $article_type ],
            [ $title_attribute, $article_type ],
            [ $workflow_step_attribute, $article_type ]
        ];
    }
}
