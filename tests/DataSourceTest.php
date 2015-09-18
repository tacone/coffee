<?php

namespace Tacone\Coffee\Test;

use Tacone\Coffee\DataSource\DataSource;

class DataSourceTest extends BaseTestCase
{
    public function testGet()
    {
        $array = [
            'apple' => 'Apples',
            'banana' => 'Bananas',
        ];
        $source = DataSource::make($array);

        assertSame(null, $source['what']);
        assertSame('Apples', $source['apple']);
        assertSame('Bananas', $source['banana']);
        assertSame(null, $source['w']);
        assertSame(null, $source['Bananas.what']);

        // third level
        $array = [
            'apple' => 'Apples',
            'banana' => [
                'cherry' => 'Cherries',
            ],
        ];
        $source = DataSource::make($array);

        assertSame(null, $source['what']);
        assertSame('Apples', $source['apple']);
        assertSame(null, $source['Bananas.what']);
        assertSame(['cherry' => 'Cherries'], $source['banana']);
        assertSame('Cherries', $source['banana.cherry']);
        assertSame(null, $source['banana.cherry.what']);
        // third level
        $array = [
            'apple' => 'Apples',
            'banana' => [
                'cherry' => [
                    'date' => 'Dates',
                ],
            ],
        ];
        $source = DataSource::make($array);

        assertSame(null, $source['what']);
        assertSame('Apples', $source['apple']);
        assertSame(null, $source['Bananas.what']);
        assertSame(['cherry' => ['date' => 'Dates']], $source['banana']);
        assertSame(null, $source['banana.cherry.what']);
        assertSame(['date' => 'Dates'], $source['banana.cherry']);
        assertSame('Dates', $source['banana.cherry.date']);
    }

    function testIsset()
    {
        $array = [
            'apple' => 'Apples',
            'banana' => [
                'cherry' => [
                    'date' => 'Dates',
                ],
            ],
        ];
        
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
}
