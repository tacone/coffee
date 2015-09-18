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

        assertSame('Apples', $source['apple']);
        assertSame(null, $source['Bananas.what']);
        assertSame(['cherry' => ['date' => 'Dates']], $source['banana']);
        assertSame(null, $source['banana.cherry.what']);
        assertSame(['date' => 'Dates'], $source['banana.cherry']);
        assertSame('Dates', $source['banana.cherry.date']);
    }
}
