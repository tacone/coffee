<?php

namespace Tacone\Coffee\Test;

use Illuminate\Database\Eloquent\Collection;
use Tacone\Coffee\DataSource\DataSource;
use Tacone\Coffee\DataSource\EloquentModelDataSource;

class EloquentModelTest extends BaseTestCase
{
    public function testMake()
    {
        assertInstanceOf(EloquentModelDataSource::class, DataSource::make(new Customer()));
    }

    public function testHasMany()
    {

        // --- test behavior

        // notice we use separate objects the two, we just want to test similar behavior
        // not interation

        $customer = new Customer();
        $source = Datasource::make(new Customer());
        $source['name'] = 'Frank';
        $source['surname'] = 'Sinatra';

        // let's try to follow the behaviour of eloquent as close as we can
        assertFalse(isset($customer->orders));
        assertFalse(isset($source->orders));
        assertInstanceOf(Collection::class, $customer->orders);
        assertInstanceOf(Collection::class, $source['orders']);

        $this->createModels(Order::class, [
            ['code' => 'a1', 'shipping' => 'home', 'customer_id' => 1],
            ['code' => 'b1', 'shipping' => 'office', 'customer_id' => 1],
        ]);

        // --- test load

        $customer = new Customer();
        $source = Datasource::make(new Customer());
        $source['id'] = 1;
        $source['name'] = 'Frank';
        $source['surname'] = 'Sinatra';
        $source->save();

        // yes, that's how eloquent behaves
        assertFalse(isset($customer->orders));
        assertFalse(isset($source->orders));
        assertInstanceOf(Collection::class, $customer->orders);
        assertInstanceOf(Collection::class, $source['orders']);
        assertModelArrayEqual(Order::all(), $source['orders']);

        // don't do this at home, folks :)
        assertSame('Frank', $source['orders.0.customer.name']);
        assertSame('Frank', $source['orders.0.customer.orders.0.customer.name']);

        // test creation
        $customer = new Customer();
        $source = Datasource::make($customer);
        $source['id'] = 1;
        $source['name'] = 'Frank';
        $source['surname'] = 'Sinatra';
        $source['orders.0.code'] = 'a1';
        $source['orders.0.shipping'] = 'home';
        $source['orders.1.code'] = 'b1';
        $source['orders.1.shipping'] = 'office';
        try {
            $source->save();
        } catch (\Exception $e) {
            dd($source->getDelegatedStorage());
        }
    }

//    TODO
//    public function testArraize()
//    {
//        $customer = new Customer();
//        $customer->name = 'Frank';
//        $customer->surname = 'Sinatra';
//        $source = DataSource::make($customer);
//        assertSame([
//            'name' => 'Frank',
//            'surname' => 'Sinatra',
//        ], $source->toArray());
//    }

    public function testGetSet()
    {
        $customer = new Customer();
        $customer->name = 'Frank';
        $customer->surname = 'Sinatra';
        $source = DataSource::make($customer);
        assertSame('Frank', $customer->name);
        assertSame('Frank Sinatra', $customer->full_name);
        assertSame('Frank', $source['name']);
        assertSame('Frank Sinatra', $source['full_name']);

        $customer = new Customer();
        $source = DataSource::make($customer);
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
        $source = DataSource::make($customer);
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
        $source = DataSource::make($customer);
        unset($source['surname']);
        assertSame('Frank', $customer->name);
        assertSame(null, $customer->surname);
        assertSame('Frank', $source['name']);
        assertSame(null, $source['surname']);

        $customer = new Customer();
        $source = DataSource::make($customer);
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
        $source = DataSource::make($customer);
        $source['name'] = 'Frank';
        $source['surname'] = 'Sinatra';
        $source['details.biography'] = 'A nice life!';
        $source['details.accepts_cookies'] = 0;
        $source['details.accepts_cookies'] = 0; // test cache

        assertSame('Frank', $source['name']);
        assertSame('A nice life!', $source['details.biography']);
        assertSame(0, $source['details.accepts_cookies']);

        $source->save();

        assertSame(1, Customer::all()->count());
        assertSame(1, CustomerDetail::all()->count());

        // test everything's saved
        $result = Customer::all()->first();
        assertSame('A nice life!', $result->details->biography);
        assertEquals(0, $result->details->accepts_cookies);

        // test we don't create duplicates

        $source['surname'] = 'Sinatrax';
        $source['details.biography'] = 'prefers not say';
        $source['details.accepts_cookies'] = 1;

        assertSame(1, Customer::all()->count());
        assertSame(1, CustomerDetail::all()->count());

        return;
    }

    public function testBelongsToOne()
    {
        $details = new CustomerDetail();
        $source = DataSource::make($details);
        $source['biography'] = 'A nice life!';
        $source['accepts_cookies'] = 0;
        $source['customer.name'] = 'Frank';
        $source['customer.surname'] = 'Sinatra';
        assertSame('A nice life!', $source['biography']);
        assertSame(0, $source['accepts_cookies']);
        $source->save();
        assertSame(1, Customer::all()->count());
        assertSame(1, CustomerDetail::all()->count());

        // test that we don't create duplicates
        $source['biography'] = 'prefers not say';
        $source['customer.name'] = 'Frank';

        assertSame(1, Customer::all()->count());
        assertSame(1, CustomerDetail::all()->count());
    }
}
