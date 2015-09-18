<?php

namespace Tacone\Coffee\Test;

use Tacone\Coffee\DataSource\DataSource;

class DataSourceTest extends BaseTestCase
{

    protected function make(array $var)
    {
        return $var;
    }

    public function testToArray()
    {
        $array = [
            'apple' => 'Apples',
            'banana' => 'Bananas',
        ];
        $object = $this->make($array);
        $source = DataSource::make($object);
        assertSame($array, $source->toArray());

        $array = [];
        $object = $this->make($array);
        $source = DataSource::make($object);
        assertSame($array, $source->toArray());

//        $source = DataSource::make('sdds');
//        try {
////            assertSame([], $source->toArray());
//            assertTrue(false, 'Scalars should never be converted to array');
//        } catch(\Exception $e) {
//
//        }
    }

    public function testUnwrap()
    {
        // we should check not to accidentally return
        // a wrapped object of some kind
        $array = $this->make([
            'apple' => 'Apples',
            'banana' => 'Bananas',
        ]);
        $source = DataSource::make($array);

        assertSame($array, $source->unwrap());
    }

    public function testGet()
    {
        $array = $this->make([
            'apple' => 'Apples',
            'banana' => 'Bananas',
        ]);
        $source = DataSource::make($array);

        assertSame(null, $source['what']);
        assertSame('Apples', $source['apple']);
        assertSame('Bananas', $source['banana']);
        assertSame(null, $source['w']);
        assertSame(null, $source['Bananas.what']);

        // third level
        $array = $this->make([
            'apple' => 'Apples',
            'banana' => [
                'cherry' => 'Cherries',
            ],
        ]);
        $source = DataSource::make($array);

        assertSame(null, $source['what']);
        assertSame('Apples', $source['apple']);
        assertSame(null, $source['Bananas.what']);
        assertEquals($this->make(['cherry' => 'Cherries']), $source['banana']);
        assertSame('Cherries', $source['banana.cherry']);
        assertSame(null, $source['banana.cherry.what']);

        // third level
        $array = $this->make([
            'apple' => 'Apples',
            'banana' => [
                'cherry' => [
                    'date' => 'Dates',
                ],
            ],
        ]);
        $source = DataSource::make($array);

        assertSame(null, $source['what']);
        assertSame('Apples', $source['apple']);
        assertSame(null, $source['Bananas.what']);
        assertEquals($this->make(['cherry' => ['date' => 'Dates']]), $source['banana']);
        assertSame(null, $source['banana.cherry.what']);
        assertEquals($this->make(['date' => 'Dates']), $source['banana.cherry']);
        assertSame('Dates', $source['banana.cherry.date']);

        /**
         * Edge cases
         */

        // Php casts 0 to false
        $array = $this->make([
            0 => [
                'apple' => 'Apples'
            ],
            'banana' => [
                '0' => 'Zero'
            ],
        ]);
        $source = DataSource::make($array);
        assertSame('Zero', $source['banana.0']);
        assertSame('Apples', $source['0.apple']);
        assertSame(null, $source['banana.0.what']);
        assertSame(null, $source['0.apple.what']);
    }

    function testIsset()
    {
        $array = $this->make([
            'apple' => 'Apples',
            'banana' => [
                'cherry' => [
                    'date' => 'Dates',
                ],
            ],
        ]);

        $source = DataSource::make($array);

        assertSame(false, isset($source['what']));
        assertSame(true, isset($source['apple']));
        assertSame(true, isset($source['banana']));
        assertSame(true, isset($source['banana.cherry']));
        assertSame(true, isset($source['banana.cherry.date']));

        assertSame(false, isset($source['apple.what']));
        assertSame(false, isset($source['banana.what']));
        assertSame(false, isset($source['banana.cherry.what']));
        assertSame(false, isset($source['banana.cherry.date.what']));
    }

    function testUnset()
    {
        $backup = $this->make([
            'apple' => 'Apples',
            'banana' => [
                'cherry' => [
                    'date' => 'Dates',
                ],
            ],
        ]);

        $array = $backup;
        $source = DataSource::make($array);
        unset($source['what']);
        assertSame(true, isset($source['apple']));

        $array = $backup;
        $source = DataSource::make($array);
        unset($source['apple']);
        assertSame(false, isset($source['apple']));
    }

    function testSet()
    {
        $array = $this->make([
            'apple' => 'Apples',

        ]);

        $source = DataSource::make($array);
        $source['banana'] = 'Bananas';
        assertSame('Bananas', $source['banana']);
        assertSame([
            'apple' => 'Apples',
            'banana' => 'Bananas',
        ], $source->toArray());

        // multi step creation
        $array = $this->make([]);
        $source = DataSource::make($array);
        $source['apple.b.c.d'] = 'hello';
//        dd($source['apple']);
        assertSame('hello', $source['apple.b.c.d']);
    }
}
