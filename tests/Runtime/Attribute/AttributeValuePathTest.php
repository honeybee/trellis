<?php

namespace Trellis\Tests\Runtime\Attribute;

use Trellis\Runtime\Attribute\AttributeValuePath;
use Trellis\Tests\Runtime\Fixtures\ArticleType;
use Trellis\Tests\Runtime\Fixtures\Paragraph;
use Trellis\Tests\TestCase;

class AttributeValuePathTest extends TestCase
{
    /**
     * @dataProvider articleValuePathProvider
     */
    public function testFoo($article, $value_path, $expected_value)
    {
        $value = AttributeValuePath::getAttributeValueByPath($article, $value_path);
        $this->assertEquals($expected_value, $value);
    }

    public function articleValuePathProvider()
    {
        $article_type = new ArticleType();
        $headline = 'test it';
        $content = 'most sophisticated cmf ever being tested here!';
        $paragraph_title = 'this is an awesome paragraph';
        $paragraph_content = 'and even more awesome content ...';

        $article = $article_type->createEntity(
            [
                'headline' => $headline,
                'content' => $content,
                'content_objects' => [
                    [
                        '@type' => 'paragraph',
                        'title' => $paragraph_title,
                        'content' => $paragraph_content
                    ]
                ]
            ]
        );

        return [
            [ $article, 'headline', $headline ],
            [ $article, 'content_objects.paragraph[0].content', $paragraph_content ],
            [ $article, 'content_objects.*[0].title', $paragraph_title ]
        ];
    }
}
