<?php

namespace Tacone\Coffee\Test;

use Illuminate\Database\Eloquent\Model;
use Tacone\Coffee\DataSource\DataSource;
use Tacone\Coffee\DataSource\EloquentCollectionDataSource;

class EloquentCollectionTest extends BaseTestCase
{
    public function testMake()
    {
        $source = DataSource::make(Order::all());
        assertInstanceOf(EloquentCollectionDataSource::class, $source);

        $this->createModels(Order::class, [
            ['code' => 'a1', 'shipping' => 'home', 'customer_id' => 1],
            ['code' => 'b1', 'shipping' => 'office', 'customer_id' => 1],
        ]);

        $source = DataSource::make(Order::all());
        assertInstanceOf(EloquentCollectionDataSource::class, $source);
    }

    public function testToArray()
    {
        $this->createModels(Order::class, $array = []);
        $source = DataSource::make(Order::all());
        assertModelArrayEqual($array, $source->toArray());

        $this->createModels(Order::class, $array = [
            ['code' => 'a1', 'shipping' => 'home', 'customer_id' => 1],
            ['code' => 'b1', 'shipping' => 'office', 'customer_id' => 1],
        ]);
        $source = DataSource::make(Order::all());
        assertModelArrayEqual($array, $source->toArray());
    }

    public function testGetSet()
    {
        $this->createModels(Order::class, $array = []);
        $source = DataSource::make(Order::all());

        assertSame(null, $source[0]);
        assertSame(null, $source['0.code']);
        assertSame(null, $source['0.shipping']);

        $this->createModels(Order::class, $array = [
            ['code' => 'a1', 'shipping' => 'home', 'customer_id' => 1],
            ['code' => 'b1', 'shipping' => 'office', 'customer_id' => 1],
        ]);

        $this->createModels(Order::class, []);
        $source = DataSource::make(Order::all());
        $source->bindToModel(new Order());

        assertSame(null, $source[0]);
        $source['0.code'] = 'a1';
        $source['0.shipping'] = 'home';
        $source['0.customer_id'] = 1;
        assertInstanceOf(Model::class, $source[0]);
        assertSame('home', $source['0.shipping']);
        assertSame('a1', $source['0.code']);

        $source['1.code'] = 'b1';
        $source['1.shipping'] = 'office';
        $source['1.customer_id'] = 2;
        assertInstanceOf(Model::class, $source[1]);
        assertSame('b1', $source['1.code']);
        assertSame('office', $source['1.shipping']);

        $source->save();
        assertModelArrayEqual($source->toArray(), Order::all()->toArray());

        // will the update work?

        $source['0.code'] = 'a1x';
        $source['1.code'] = 'b1x';
        assertSame('a1x', $source['0.code']);
        assertSame('b1x', $source['1.code']);

        $source->save();
        assertModelArrayEqual($source->toArray(), Order::all()->toArray());
    }

    public function testCreateException()
    {
        $this->setExpectedException(\RuntimeException::class);

        $this->createModels(Order::class, []);
        $source = DataSource::make(Order::all());
        assertSame(null, $source[0]);
        $source['0.code'] = 'a1';

        return;
    }

    public function testUnset()
    {
        $this->createModels(Order::class, $array = [
            ['code' => 'a1', 'shipping' => 'home', 'customer_id' => 1],
            ['code' => 'b1', 'shipping' => 'office', 'customer_id' => 1],
        ]);
        $collection = Order::all();
        $source = DataSource::make($collection);

        assertInstanceOf(Order::class, $source[0]);
        assertInstanceOf(Order::class, $source[1]);
        assertInstanceOf(Order::class, $collection[0]);
        assertInstanceOf(Order::class, $collection[1]);
        unset($source[0]);
        assertNull($source[0]);
        assertInstanceOf(Order::class, $source[1]);
        assertTrue(!isset($collection[0]));
        assertInstanceOf(Order::class, $collection[1]);
        unset($source[1]);
        assertNull($source[1]);
        assertTrue(!isset($collection[1]));
    }
}
