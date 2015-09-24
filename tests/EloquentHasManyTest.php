<?php

namespace Tacone\Coffee\Test;

use Illuminate\Database\Eloquent\Collection;
use Tacone\Coffee\DataSource\DataSource;

class EloquentHasManyTest extends BaseTestCase
{
    public function testest()
    {
    }
//    public function testHasMany()
//    {
//        // --- test behavior
//
//        // notice we use separate objects the two, we just want to test similar behavior
//        // not interation
//
//        $customer = new Customer();
//        $source = Datasource::make(new Customer());
//        $source['name'] = 'Frank';
//        $source['surname'] = 'Sinatra';
//
//        // let's try to follow the behaviour of eloquent as close as we can
//        assertFalse(isset($customer->orders));
//        assertFalse(isset($source->orders));
//        assertInstanceOf(Collection::class, $customer->orders);
//        assertInstanceOf(Collection::class, $source['orders']);
//    }
//
//    public function testExistingChildren()
//    {
//
//        // --- test load
//        $this->createModels(Customer::class, []);
//        $this->createModels(Order::class, [
//            ['code' => 'a1', 'shipping' => 'home', 'customer_id' => 1],
//            ['code' => 'b1', 'shipping' => 'office', 'customer_id' => 1],
//        ]);
//
//        $customer = new Customer();
//        $source = Datasource::make(new Customer());
//        $source['id'] = 1;
//        $source['name'] = 'Frank';
//        $source['surname'] = 'Sinatra';
//        $source->save();
//
//        // yes, that's how eloquent behaves
//        assertFalse(isset($customer->orders));
//        assertFalse(isset($source->orders));
//        assertInstanceOf(Collection::class, $customer->orders);
//        assertInstanceOf(Collection::class, $source['orders']);
//        assertModelArrayEqual(Order::all(), $source['orders']);
//
//        // don't do this at home, folks :)
//        assertSame('Frank', $source['orders.0.customer.name']);
//        assertSame('Frank', $source['orders.0.customer.orders.0.customer.name']);
//
//    }
//
//    public function testCreateChildren()
//    {
//        $this->createModels(Customer::class, []);
//        $this->createModels(Order::class, []);
//
//        // test creation
//        $customer = new Customer();
//        $source = Datasource::make($customer);
//        $source['id'] = 1;
//        $source['name'] = 'Frank';
//        $source['surname'] = 'Sinatra';
//        $source['orders.0.code'] = 'a1';
//        $source['orders.0.shipping'] = 'home';
//        $source['orders.1.code'] = 'b1';
//        $source['orders.1.shipping'] = 'office';
//        $source->save();
//
//        try {
//        } catch (\Exception $e) {
//            dd($source->getDelegatedStorage());
//        }
//    }
}
