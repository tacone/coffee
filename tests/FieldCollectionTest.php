<?php

namespace Tacone\Coffee\Test;

use Tacone\Coffee\Collection\FieldCollection;
use Tacone\Coffee\Field\Text;

class FieldCollectionTest extends ZTestCase
{

    protected function object()
    {
        return new FieldCollection();
    }

    public function testAddField()
    {
        $fields = $this->object();
        // there should be no fields now
        $this->assertEquals(0, count($fields));
        $name = 'title';
        // add a field
        $fields->push( new Text($name) );
        $this->assertEquals(1, count($fields));
        
        $this->assertTrue(isset($fields[$name]));
        $this->assertTrue($fields->has($name));
        
        // method 1: offsetGet
        $f = $fields[$name];
        $this->assertInstanceOf('\Tacone\Coffee\Field\Text', $f);
        
        // method 2: get()
//        $f = $fields->get($name);
//        $this->assertInstanceOf('\Tacone\Coffee\Field\Text', $f);
        
        // make sure arguments are passed on
        $this->assertEquals($name, $f->name());
        
    }

}
