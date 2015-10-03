<?php

namespace ActiveCollab\DatabaseStructure\Test;

use ActiveCollab\DatabaseStructure\Type;
use ActiveCollab\DatabaseStructure\Field\Composite\IsArchivedField;
use ActiveCollab\DatabaseStructure\Behaviour\IsArchivedInterface;
use ActiveCollab\DatabaseStructure\Behaviour\IsArchivedInterface\Implementation as IsArchivedInterfaceImplementation;

/**
 * @package ActiveCollab\DatabaseStructure\Test
 */
class IsArchivedFieldTest extends TestCase
{
    /**
     * Test if is_archived is default field name
     */
    public function testDefaultName()
    {
        $this->assertEquals('is_archived', (new IsArchivedField())->getName());
    }

    /**
     * Test if FALSE is the default value
     */
    public function testFalseIsDefaultValue()
    {
        $this->assertSame(false, (new IsArchivedField())->getDefaultValue());
    }

    /**
     * Test if is_archived can be added to a type
     */
    public function testIsArchiveCanBeAddedToType()
    {
        $type = (new Type('chapters'))->addField(new IsArchivedField());

        $this->assertArrayHasKey('is_archived', $type->getFields());
        $this->assertInstanceOf(IsArchivedField::class, $type->getFields()['is_archived']);
    }

    /**
     * Test if cascade property is FALSE by default
     */
    public function testCascadeIsFalseByDefault()
    {
        $this->assertSame(false, (new IsArchivedField())->getCascade());
    }

    /**
     * Test if cascade can be changed to TRUE
     */
    public function testCascadeCanBeChanged()
    {
        $this->assertSame(true, (new IsArchivedField())->cascade()->getCascade());
    }

    /**
     * Test if original_is_archived field is not added when is_archived is not cascaded
     */
    public function testIsArchivedDoesNotAddOriginalFieldByDefault()
    {
        $type = (new Type('chapters'))->addField(new IsArchivedField());

        $fields = $type->getAllFields();

        $this->assertCount(2, $fields);

        $this->assertArrayHasKey('id', $fields);
        $this->assertArrayHasKey('is_archived', $fields);
        $this->assertArrayNotHasKey('original_is_archived', $fields);
    }

    /**
     * Test if original_is_archived field is added when is_archived is cascaded
     */
    public function testIsArchivedAddsOriginalFieldWhenCascaded()
    {
        $type = (new Type('chapters'))->addField((new IsArchivedField())->cascade());

        $fields = $type->getAllFields();

        $this->assertCount(3, $fields);

        $this->assertArrayHasKey('id', $fields);
        $this->assertArrayHasKey('is_archived', $fields);
        $this->assertArrayHasKey('original_is_archived', $fields);
    }

    /**
     * Test if is_archived field adds behaviour to the type
     */
    public function testPositionFieldAddsBehaviourToType()
    {
        $type = (new Type('chapters'))->addField(new IsArchivedField());

        $this->assertArrayHasKey(IsArchivedInterface::class, $type->getTraits());
        $this->assertContains(IsArchivedInterfaceImplementation::class, $type->getTraits()[IsArchivedInterface::class]);
    }
}