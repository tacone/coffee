<?php

namespace Tacone\Coffee\Test;

use Tacone\Coffee\DataSource\OldDataSource;

class OldDataSourceTest extends BaseTestCase
{
    public function testHasMany()
    {
        //        $customer = new Customer();
//        $source = new DataSource($customer);
//        $source['name'] = 'Frank';
//        $source['surname'] = 'Sinatra';
//        $source['orders.0.code'] = 'A1';
//        assertSame('A1', $source['orders.0.code']);
//
//        $a = null;
//        die;
//        dd($source->find('orders.0', $a));
//        dd($source['orders']);
//        dd($source);
//        dd($source->toArray());
    }

    public function testGetSet()
    {
        $customer = new Customer();
        $customer->name = 'Frank';
        $customer->surname = 'Sinatra';
        $source = new OldDataSource($customer);
        assertSame('Frank', $customer->name);
        assertSame('Frank Sinatra', $customer->full_name);
        assertSame('Frank', $source['name']);
        assertSame('Frank Sinatra', $source['full_name']);

        $customer = new Customer();
        $source = new OldDataSource($customer);
        $source['name'] = 'Frank';
        $source['surname'] = 'Sinatra';
        assertSame('Frank', $customer->name);
        assertSame('Frank Sinatra', $customer->full_name);
        assertSame('Frank', $source['name']);
        assertSame('Frank Sinatra', $source['full_name']);
        $source->save();
        assertSame(1, Customer::all()->count());

        // now let's check if the update works
        $source['name'] = 'Jake';
        assertSame('Jake', $source['name']);
        $source->save();
        assertSame(1, Customer::all()->count());
        assertSame('Jake', Customer::find($source['id'])->name);

        // again, and to test cache collisions
        $customer = new Customer();
        $source = new OldDataSource($customer);
        $source['name'] = 'Brandon';
        $source['surname'] = 'Lee';

        assertSame('Brandon', $customer->name);
        assertSame('Brandon Lee', $customer->full_name);
        assertSame('Brandon', $source['name']);
        assertSame('Brandon Lee', $source['full_name']);
        $source->save();

        assertSame(2, Customer::all()->count());
    }

    public function testUnset()
    {
        $customer = new Customer();
        $customer->name = 'Frank';
        $customer->surname = 'Sinatra';
        $source = new OldDataSource($customer);
        unset($source['surname']);
        assertSame('Frank', $customer->name);
        assertSame(null, $customer->surname);
        assertSame('Frank', $source['name']);
        assertSame(null, $source['surname']);

        $customer = new Customer();
        $source = new OldDataSource($customer);
        $source['name'] = 'Frank';
        $source['surname'] = 'Sinatra';
        unset($source['surname']);
        assertSame('Frank', $customer->name);
        assertSame(null, $customer->surname);
        assertSame('Frank', $source['name']);
        assertSame(null, $source['surname']);
    }

    public function testHasOne()
    {
        $customer = new Customer();
        $source = new OldDataSource($customer);
        $source['name'] = 'Frank';
        $source['surname'] = 'Sinatra';
        $source['details.biography'] = 'A nice life!';
        $source['details.accepts_cookies'] = 0;

        assertSame('Frank', $source['name']);
        assertSame('Frank Sinatra', $source['full_name']);
        $source->save();
        assertSame(1, Customer::all()->count());
        assertSame(1, CustomerDetail::all()->count());

        // test we don't create duplicates

        $source['surname'] = 'Sinatrax';
        $source['details.biography'] = 'prefers not say';
        $source['details.accepts_cookies'] = 1;

        assertSame(1, Customer::all()->count());
        assertSame(1, CustomerDetail::all()->count());
    }

    public function testBelongsToOne()
    {
        $details = new CustomerDetail();
        $source = new OldDataSource($details);
        $source['biography'] = 'A nice life!';
        $source['accepts_cookies'] = 0;
        $source['customer.name'] = 'Frank';
        $source['customer.surname'] = 'Sinatra';
        assertSame('A nice life!', $source['biography']);
        assertSame(0, $source['accepts_cookies']);
        $source->save();
        assertSame(1, Customer::all()->count());
        assertSame(1, CustomerDetail::all()->count());

        // test we don't create duplicates

        $source['biography'] = 'prefers not say';
        $source['customer.name'] = 'Frank';

        assertSame(1, Customer::all()->count());
        assertSame(1, CustomerDetail::all()->count());
    }
}
