<?php

namespace Tacone\Coffee\Test;

use Illuminate\Database\Eloquent\Model;
use Tacone\Coffee\DataSource\DataSource;
use Tacone\Coffee\DataSource\EloquentCollectionDataSource;

class EloquentCollectionTest extends BaseTestCase
{
    public function make($className, $data)
    {
        (new $className())->truncate();

        foreach ($data as $record) {
            $model = new $className();
            foreach ($record as $key => $value) {
                $model->$key = $value;
            }
            $model->save();
        }
    }

    public function testMake()
    {
        $source = DataSource::make(Order::all());
        assertInstanceOf(EloquentCollectionDataSource::class, $source);

        $this->make(Order::class, [
            ['code' => 'a1', 'shipping' => 'home', 'customer_id' => 1],
            ['code' => 'b1', 'shipping' => 'office', 'customer_id' => 1],
        ]);

        $source = DataSource::make(Order::all());
        assertInstanceOf(EloquentCollectionDataSource::class, $source);
    }

    public function testToArray()
    {
        $this->make(Order::class, $array = []);
        $source = DataSource::make(Order::all());
        assertModelArrayEqual($array, $source->toArray());

        $this->make(Order::class, $array = [
            ['code' => 'a1', 'shipping' => 'home', 'customer_id' => 1],
            ['code' => 'b1', 'shipping' => 'office', 'customer_id' => 1],
        ]);
        $source = DataSource::make(Order::all());
        assertModelArrayEqual($array, $source->toArray());
    }

    public function testGetSet()
    {
        $this->make(Order::class, $array = []);
        $source = DataSource::make(Order::all());

        assertSame(null, $source[0]);
        assertSame(null, $source['0.code']);
        assertSame(null, $source['0.shipping']);

        $this->make(Order::class, $array = [
            ['code' => 'a1', 'shipping' => 'home', 'customer_id' => 1],
            ['code' => 'b1', 'shipping' => 'office', 'customer_id' => 1],
        ]);

        return;

        $this->make(Order::class, []);
        $source = DataSource::make(Order::all());
        assertSame(null, $source[0]);
        $source['0.code'] = 'a1';
        assertInstanceOf(Model::class, $source[0]);

        return;
        assertSame('home', $source['0.shipping']);
        assertSame('b1', $source['1.code']);
        assertSame('office', $source['1.shipping']);

////
//        $customer = new Customer();
//        $source = DataSource::make($customer);
//        $source['name'] = 'Frank';
//        $source['surname'] = 'Sinatra';
//        assertSame('Frank', $customer->name);
//        assertSame('Frank Sinatra', $customer->full_name);
//        assertSame('Frank', $source['name']);
//        assertSame('Frank Sinatra', $source['full_name']);
//
//        $source->save();
//        assertSame(1, Customer::all()->count());
//
//        // now let's check if the update works
//        $source['name'] = 'Jake';
//        assertSame('Jake', $source['name']);
//        $source->save();
//        assertSame(1, Customer::all()->count());
//        assertSame('Jake', Customer::find($source['id'])->name);
//
//        // again, and to test cache collisions
//        $customer = new Customer();
//        $source = DataSource::make($customer);
//        $source['name'] = 'Brandon';
//        $source['surname'] = 'Lee';
//
//        assertSame('Brandon', $customer->name);
//        assertSame('Brandon Lee', $customer->full_name);
//        assertSame('Brandon', $source['name']);
//        assertSame('Brandon Lee', $source['full_name']);
//        $source->save();
//
//        assertSame(2, Customer::all()->count());
    }
//
//    public function testUnset()
//    {
//        $customer = new Customer();
//        $customer->name = 'Frank';
//        $customer->surname = 'Sinatra';
//        $source = DataSource::make($customer);
//        unset($source['surname']);
//        assertSame('Frank', $customer->name);
//        assertSame(null, $customer->surname);
//        assertSame('Frank', $source['name']);
//        assertSame(null, $source['surname']);
//
//        $customer = new Customer();
//        $source = DataSource::make($customer);
//        $source['name'] = 'Frank';
//        $source['surname'] = 'Sinatra';
//        unset($source['surname']);
//        assertSame('Frank', $customer->name);
//        assertSame(null, $customer->surname);
//        assertSame('Frank', $source['name']);
//        assertSame(null, $source['surname']);
//    }
}
