<?php

namespace Tacone\Coffee\Test;

use Tacone\Coffee\Widget\DataForm;

class DataFormTest extends ZTestCase
{
    protected function widget()
    {
        return new DataForm();
    }

    public function testAddField()
    {
        $form = new DataForm();
        // there should be no fields now
        $this->assertEquals(0, count($form->fields()));
        // add a field
        $field = $form->text('title', 'my title');
        // make sure the IOC container works as expected
        $this->assertInstanceOf('\Tacone\Coffee\Field\Text', $field);

        $this->assertInstanceOf('\Tacone\Coffee\Field\Text', $form->field('title'));
        // make sure arguments are passed on
        $this->assertEquals('title', $field->name());
        $this->assertEquals('my title', $field->label());
        $this->assertEquals(1, count($form->fields()));

        $this->assertSame($field, $form->field('title'));
    }

    public function testToArray()
    {
        $form = new DataForm();
        $form->text('title')->value('robert');
        $form->text('email')->value('robert@example.com');
        $this->assertSame([
            'title' => 'robert',
            'email' => 'robert@example.com',
        ], $form->toArray());
    }
}
