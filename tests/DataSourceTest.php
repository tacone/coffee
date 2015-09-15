<?php

namespace Tacone\Coffee\Test;

class DataSourceTest extends BaseTestCase
{
    public function testGet()
    {
        $c = new Customer();
        $c->name = 'Frank';
        $c->surname = 'Sinatra';
        assertEquals('Frank', $c->name);
    }

    public function testGet2()
    {
        $c = new Customer();
        $c->name = 'Frank';
        $c->surname = 'Sinatra';
        assertEquals('Frank', $c->name);
    }
}
