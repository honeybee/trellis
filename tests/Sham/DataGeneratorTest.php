<?php

namespace Trellis\Tests\Runtime\Sham;

use Trellis\Sham\DataGenerator;
use Trellis\Runtime\Entity\EntityInterface;
use Trellis\Common\Error\RuntimeException;
use Trellis\Tests\TestCase;
use Trellis\Tests\Runtime\Fixtures\ArticleType;

class DataGeneratorTest extends TestCase
{
    protected $type;
    protected $entity;

    public function setUp()
    {
        $this->type = new ArticleType();
        $this->entity = $this->type->createEntity();
    }

    public function testDefaultEntity()
    {
        $this->assertInstanceOf(EntityInterface::CLASS, $this->entity);
        $this->assertEquals('Article', $this->type->getName());
        $this->assertEquals(
            17,
            $this->type->getAttributes()->getSize(),
            'Number of attributes is unexpected. Please adjust tests if new attributes were introduced.'
        );
        $this->assertEquals(
            $this->type->getAttributes()->getSize(),
            count($this->type->getAttributes()),
            'Number of attributes should be equal independant of used count method.'
        );
        $this->assertTrue(
            $this->entity->isClean(),
            'Entity should have no changes prior filling it with fake data'
        );
        $this->assertTrue(
            count($this->entity->getChanges()) === 0,
            'Entity should not contain changes prior test.'
        );
    }

    public function testFillEntity()
    {
        DataGenerator::fill($this->entity);

        $this->assertFalse(
            $this->entity->isClean(),
            'Entity has no changes, but should have been filled with fake data.'
        );
    }

    public function testFillEntityClean()
    {
        DataGenerator::fill($this->entity, array(DataGenerator::OPTION_MARK_CLEAN => true));

        $this->assertTrue(
            $this->entity->isClean(),
            'Entity has changes, but the given flag should have prevented that.'
        );
        $this->assertTrue(count($this->entity->getChanges()) === 0);
    }

    public function testFillEntityWithClosure()
    {
        DataGenerator::fill(
            $this->entity,
            array(
                DataGenerator::OPTION_LOCALE => 'de_DE',
                DataGenerator::OPTION_FIELD_VALUES => array(
                    'author' => function () {
                        return 'trololo';
                    }
                )
            )
        );

        $this->assertFalse(
            $this->entity->isClean(),
            'Entity has no changes, but should have been filled with fake data.'
        );

        $this->assertEquals($this->entity->getValue('author'), 'trololo');
    }

    public function testFillEntityWithCallable()
    {
        DataGenerator::fill(
            $this->entity,
            array(
                DataGenerator::OPTION_LOCALE => 'de_DE',
                DataGenerator::OPTION_FIELD_VALUES => array(
                    'author' => array($this, 'getTrololo')
                )
            )
        );

        $this->assertFalse(
            $this->entity->isClean(),
            'Entity has no changes, but should have been filled with fake data.'
        );

        $this->assertTrue($this->entity->getValue('author') === 'trololo');
    }

    public function testFillEntityWithStaticCallable()
    {
        DataGenerator::fill(
            $this->entity,
            array(
                DataGenerator::OPTION_LOCALE => 'de_DE',
                DataGenerator::OPTION_FIELD_VALUES => array(
                    'author' => 'Trellis\\Tests\\Runtime\\Sham\\DataGeneratorTest::getStaticTrololo'
                )
            )
        );

        $this->assertFalse(
            $this->entity->isClean(),
            'Entity has no changes, but should have been filled with fake data.'
        );

        $this->assertEquals($this->entity->getValue('author'), 'trololo');
    }

    public function testFillEntityWithMultipleClosures()
    {
        $faker = \Faker\Factory::create('en_UK');
        $fake_author = function () use ($faker) {
            return $faker->name;
        };
        $fake_headline = function () use ($faker) {
            return $faker->sentence;
        };
        $fake_content = function () use ($faker) {
            return $faker->paragraphs(4, true);
        };

        DataGenerator::fill(
            $this->entity,
            array(
                DataGenerator::OPTION_LOCALE => 'de_DE',
                DataGenerator::OPTION_FIELD_VALUES => array(
                    'headline' => $fake_headline,
                    'content' => $fake_content,
                    'author' => $fake_author,
                    'images' => array(1,2,3,4),
                    'click_count' => 1337,
                    'missing' => 'asdf'
                )
            )
        );

        $this->assertFalse(
            $this->entity->isClean(),
            'Entity has no changes, but should have been filled with fake data.'
        );

        $expected_image_count = 4;
        $image_count = count($this->entity->getValue('images'));
        $this->assertEquals($expected_image_count, $image_count);

        $expected_click_count = 1337;
        $this->assertEquals($expected_click_count, 1337);
    }

    public function testFillEntityBoolean()
    {
        DataGenerator::fill($this->entity);

        $this->assertTrue(
            is_bool($this->entity->getValue('enabled')),
            'Enabled attribute should have a boolean value.'
        );
    }

    public function testFillEntityTextCollection()
    {
        DataGenerator::fill($this->entity);

        $this->assertFalse(
            $this->entity->isClean(),
            'Entity has changes, but the given flag should have prevented that.'
        );

        $this->assertTrue(
            is_array($this->entity->getValue('keywords')),
            'Keywords value should be an array as that attribute is an instance of TextCollection'
        );

        $this->assertGreaterThanOrEqual(
            1,
            count($this->entity->getValue('keywords')),
            'At least one keyword should be set.'
        );
    }

    public function testFillEntityEmbed()
    {
        $data = DataGenerator::createDataFor($this->type);
        $this->assertTrue(is_array($data['content_objects']), 'The Article should have a content_object.');
        $paragraph_data = $data['content_objects'][0];

        $this->assertArrayHasKey('title', $paragraph_data, 'The Paragraph should have a title attribute.');
        $this->assertTrue(!empty($paragraph_data['title']), 'The title of the Paragraph should not be empty.');
        $this->assertArrayHasKey('content', $paragraph_data, 'The Paragraph should have a content attribute.');
    }

    public function testFillEntityGuessTextEmail()
    {
        DataGenerator::fill($this->entity);

        $this->assertFalse(
            $this->entity->isClean(),
            'Entity has no changes, but should have been filled with fake data.'
        );

        $email = $this->entity->getValue('email');
        $this->assertEquals($email, filter_var($email, FILTER_VALIDATE_EMAIL));
    }

    public function testFillEntityGuessTextAuthor()
    {
        DataGenerator::fill(
            $this->entity,
            array(DataGenerator::OPTION_LOCALE => 'de_DE')
        );

        $this->assertFalse(
            $this->entity->isClean(),
            'Entity has no changes, but should have been filled with fake data.'
        );
    }

    public function testFillEntityGuessTextAuthorDisabled()
    {
        DataGenerator::fill(
            $this->entity,
            array(
                DataGenerator::OPTION_LOCALE => 'de_DE',
                DataGenerator::OPTION_GUESS_PROVIDER_BY_NAME => false
            )
        );

        $this->assertFalse(
            $this->entity->isClean(),
            'Entity has no changes, but should have been filled with fake data.'
        );
    }

    public function testFillEntityIgnoreAttribute()
    {
        $this->assertEquals(17, $this->type->getAttributes()->getSize());
        $excluded_attributes = array('author', 'click_count', 'enabled');

        DataGenerator::fill(
            $this->entity,
            array(DataGenerator::OPTION_EXCLUDED_FIELDS => $excluded_attributes)
        );

        $this->assertFalse(
            $this->entity->isClean(),
            'Entity has no changes, but should have been filled with fake data.'
        );

        $changed_attributes = [];
        foreach ($this->entity->getChanges() as $changed_event) {
            $changed_attributes[] = $changed_event->getAttributeName();
        }

        foreach ($excluded_attributes as $excluded_attribute) {
            $this->assertFalse(in_array($excluded_attribute, $changed_attributes));
        }
    }// @codeCoverageIgnoreEnd

    /**
     * @expectedException Trellis\Common\Error\BadValueException
     * @codeCoverageIgnore
     */
    public function testInvalidLocaleForFill()
    {
        DataGenerator::fill($this->entity, array(DataGenerator::OPTION_LOCALE => 'trololo'));
    }

    /**
     * @expectedException Trellis\Common\Error\BadValueException
     * @codeCoverageIgnore
     */
    public function testInvalidLocaleForFill2()
    {
        DataGenerator::fill($this->entity, array(DataGenerator::OPTION_LOCALE => 1));
    }

    /**
     * @expectedException Trellis\Common\Error\BadValueException
     * @codeCoverageIgnore
     */
    public function testInvalidLocaleForFill3()
    {
        DataGenerator::fill($this->entity, array(DataGenerator::OPTION_LOCALE => new \stdClass()));
    }

    public function testCreateDataFor()
    {
        $data = DataGenerator::createDataFor(
            $this->type,
            array(
                DataGenerator::OPTION_FIELD_VALUES => array(
                    'missing' => 'trololo'
                )
            )
        );

        $this->assertTrue(is_array($data), 'Returned data should be an array.');
        $this->assertTrue(!empty($data), 'Returned data array should not be empty.');

        $this->assertArrayHasKey('author', $data);
        $this->assertInternalType('string', $data['author']);
        $this->assertNotEmpty($data['author']);

        $this->assertArrayHasKey('email', $data);
        $this->assertNotFalse(filter_var($data['email'], FILTER_VALIDATE_EMAIL));

        $this->assertArrayHasKey('website', $data);
        $this->assertNotFalse(filter_var($data['website'], FILTER_VALIDATE_URL));

        $this->assertArrayHasKey('headline', $data);
        $this->assertInternalType('string', $data['headline']);
        $this->assertNotEmpty($data['headline']);

        $this->assertArrayHasKey('click_count', $data);
        $this->assertInternalType('integer', $data['click_count']);

        $this->assertArrayHasKey('float', $data);
        $this->assertInternalType('float', $data['float']);

        $this->assertArrayHasKey('content', $data);
        $this->assertInternalType('string', $data['content']);
        $this->assertNotEmpty($data['content']);

        $this->assertNotEmpty($data['meta']);

        $this->assertArrayNotHasKey('missing', $data, 'Returned data should not have missing key.');

        $this->markTestIncomplete('Need more tests for nested arrays of data');
    }

    public function testCreateEntity()
    {
        $entity = DataGenerator::createEntity($this->type);

        $this->assertTrue($entity->isClean(), 'New entity should have no changes.');
        $this->assertTrue(0 === count($entity->getChanges()), 'New entity should have no changes.');
    }

    public function testCreateEntities()
    {
        $num_entities = 30;
        $entities = DataGenerator::createEntities(
            $this->type,
            array(
                DataGenerator::OPTION_COUNT => $num_entities,
                DataGenerator::OPTION_LOCALE => 'fr_FR'
            )
        );

        $this->assertTrue($num_entities === count($entities));

        for ($i = 0; $i < $num_entities; $i++) {
            $entity = $entities[$i];
            $this->assertTrue($entity->isClean(), "New entity $i should have no changes.");
            $this->assertTrue(0 === count($entity->getChanges()), "New entity $i should have no changes.");
        }
    }

    public function getTrololo()
    {
        return 'trololo';
    }

    public static function getStaticTrololo()
    {
        return 'trololo';
    }
}
