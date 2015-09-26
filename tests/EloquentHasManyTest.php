<?php

namespace Tacone\Coffee\Test;

use Illuminate\Database\Eloquent\Collection;
use Tacone\Coffee\DataSource\DataSource;

class EloquentHasManyTest extends BaseTestCase
{
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
    }

    public function testExistingChildren()
    {
        // --- test load
        $this->createModels(Customer::class, []);
        $this->createModels(Order::class, [
            ['code' => 'a1', 'shipping' => 'home', 'customer_id' => 1],
            ['code' => 'b1', 'shipping' => 'office', 'customer_id' => 1],
        ]);

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
    }

    public function testCreateChildren()
    {
        $this->createModels(Customer::class, []);
        $this->createModels(Order::class, []);

        // test creation
        $customer = new Customer();
        $source = Datasource::make($customer);
        $source['name'] = 'Frank';
        $source['surname'] = 'Sinatra';
        $source['orders.0.code'] = 'a1';
        $source['orders.0.shipping'] = 'home';
        $source['orders.1.code'] = 'b1';
        $source['orders.1.shipping'] = 'office';
        $source->save();

        assertModelArrayEqual([
            [
                'name' => 'Frank',
                'surname' => 'Sinatra',
            ],
        ], Customer::all()->toArray());

        assertModelArrayEqual([
            [
                'code' => 'a1',
                'shipping' => 'home',
                'customer_id' => 1,
            ],
            [
                'code' => 'b1',
                'shipping' => 'office',
                'customer_id' => 1,
            ],
        ], Order::all()->toArray());
    }

    public function testUpdateChildren()
    {
        // --- test load
        $this->createModels(Customer::class,  [[
            'name' => 'Frankx',
            'surname' => 'Sinatrax',
        ]]);
        $this->createModels(Order::class, [
            ['code' => 'a1x', 'shipping' => 'homex', 'customer_id' => 1],
            ['code' => 'b1x', 'shipping' => 'officex', 'customer_id' => 1],
        ]);

        $source = DataSource::make(Customer::find(1));
        $source['name'] = 'Frank';
        $source['surname'] = 'Sinatra';
        $source['orders.0.code'] = 'a1';
        $source['orders.0.shipping'] = 'home';
        $source['orders.1.code'] = 'b1';
        $source['orders.1.shipping'] = 'office';
        $source->save();

        assertEquals(1, $source['orders.0.customer_id']);
        assertEquals(1, $source['orders.1.customer_id']);

        assertModelArrayEqual([
            [
                'name' => 'Frank',
                'surname' => 'Sinatra',
            ],
        ], Customer::all()->toArray());

        assertModelArrayEqual([
            [
                'code' => 'a1',
                'shipping' => 'home',
                'customer_id' => 1,
            ],
            [
                'code' => 'b1',
                'shipping' => 'office',
                'customer_id' => 1,
            ],
        ], Order::all()->toArray());
    }
}
