<?php


namespace Tacone\Coffee\Attribute;

class CssAttribute extends DictionaryAttribute
{
    public function exposes()
    {
        return [
            'accessors' => ['has'],
            'others' => ['add', 'remove']
        ];
    }

    protected function render()
    {
        $output = [];
        foreach ($this as $key => $value) {
            if (is_numeric($key)) {
                continue;
            }
            $output[] = "$key: $value";
        }

        return implode(';', $output);
    }
}
