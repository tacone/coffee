<?php

namespace Tacone\Coffee\Test;

use Tacone\Coffee\Field\Text;

class TextTest extends ZTestCase
{
    protected function field($name, $label = null)
    {
        return new Text($name, $label);
    }

    public function testBasicExample()
    {
        $f = $this->field('title', 'A title');
        $output = $f->output($f);
        $this->assertTag([
            'tag' => 'input',
            'attributes' => [
                'name' => 'title',
            ],
            ], $output);
    }
}
