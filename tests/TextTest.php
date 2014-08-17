<?php

namespace Tacone\Coffee\Test;

use Tacone\Coffee\Field\Text;

class TextTest extends ZTestCase
{

    protected function field()
    {
        return new Text('title');
    }

    public function testBasicExample()
    {
        $f = $this->field('title');
        $output = $f->output($f);
        $this->assertTag([
            
            ], $output);
    }

}
