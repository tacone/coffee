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
        $field = $form->text('title');
        // make sure the IOC container works as expected
        $this->assertInstanceOf('\Tacone\Coffee\Field\Text', $field);
        // make sure arguments are passed on
        $this->assertEquals('title', $field->name());
        $this->assertEquals(1, count($form->fields()));
    }

}
