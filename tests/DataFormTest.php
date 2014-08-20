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
        $f = new DataForm();
        $field = $f->text('name');
        
        $this->assertInstanceOf('\Tacone\Coffee\Field\Text', $field);
    }

}
