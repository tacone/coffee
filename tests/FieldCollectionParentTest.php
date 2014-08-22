<?php

namespace Tacone\Coffee\Test;

use Tacone\Coffee\Collection\FieldCollection;
use Tacone\Coffee\Field\Text;
use Mockery as m;

//use Tacone\Coffee\Collection\FieldCollection;

class FieldCollectionParentTest extends ZTestCase
{

    public function testFirstReturnsFirstItemInCollection()
    {
        $c = new FieldCollection(array($foo = new Text('foo'), new Text('bar')));
        $this->assertSame($foo, $c->first());
    }

    public function testLastReturnsLastItemInCollection()
    {
        $c = new FieldCollection(array(new Text('foo'), $bar = new Text('bar')));

        $this->assertSame($bar, $c->last());
    }

    public function testPopReturnsAndRemovesLastItemInCollection()
    {
        $c = new FieldCollection(array($foo = new Text('foo'), $bar = new Text('bar')));

        $this->assertEquals($bar, $c->pop());
        $this->assertEquals($foo, $c->first());
    }

    public function testShiftReturnsAndRemovesFirstItemInCollection()
    {
        $c = new FieldCollection(array($foo = new Text('foo'), $bar = new Text('bar')));

        $this->assertEquals($foo, $c->shift());
        $this->assertEquals($bar, $c->first());
    }

    public function testEmptyCollectionIsEmpty()
    {
        $c = new FieldCollection();

        $this->assertTrue($c->isEmpty());
    }

    public function testToArrayCallsToArrayOnEachItemInCollection()
    {
        $item1 = m::mock('Tacone\Coffee\Field\Text');
        $item1->shouldReceive('toArray')->once()->andReturn('foo.array');
        $item1->shouldReceive('name')->once()->andReturn('foo');
        $item2 = m::mock('Tacone\Coffee\Field\Text');
        $item2->shouldReceive('toArray')->once()->andReturn('bar.array');
        $item2->shouldReceive('name')->once()->andReturn('bar');
        $c = new FieldCollection(array($item1, $item2));
        $results = $c->toArray();

        $this->assertEquals(array('foo' => 'foo.array', 'bar' => 'bar.array'), $results);
    }

    public function testToJsonEncodesTheToArrayResult()
    {
        $c = $this->getMock('Tacone\Coffee\Collection\FieldCollection', array('toArray'));
        $c->expects($this->once())->method('toArray')->will($this->returnValue($foo = new Text('foo')));
        $results = $c->toJson();

        $this->assertEquals(json_encode($foo), $results);
    }

    public function testCastingToStringJsonEncodesTheToArrayResult()
    {
        $c = $this->getMock('Illuminate\Database\Eloquent\Collection', array('toArray'));
        $c->expects($this->once())->method('toArray')->will($this->returnValue($foo = new Text('foo')));

        $this->assertEquals(json_encode($foo), (string) $c);
    }

    public function testOffsetAccess()
    {
        $c = new FieldCollection(array($foo = new Text('foo')));
        $this->assertSame($foo, $c['foo']);
        $c['foo'] = $foo2 = new Text('foo');
        $this->assertSame($foo2, $c['foo']);
        $this->assertTrue(isset($c['foo']));
        unset($c['foo']);
        $this->assertFalse(isset($c['foo']));
        $c[] = $foo3 = new Text('foo');
        $this->assertSame($foo3, $c['foo']);
    }

    public function testCountable()
    {
        $c = new FieldCollection(array(new Text('foo'), new Text('bar')));
        $this->assertEquals(2, count($c));
    }

    public function testIterable()
    {
        $c = new FieldCollection(array($foo = new Text('foo')));
        $this->assertInstanceOf('ArrayIterator', $c->getIterator());
        $this->assertSame(array('foo' => $foo), $c->getIterator()->getArrayCopy());
    }

    public function testCachingIterator()
    {
        $c = new FieldCollection(array(new Text('foo')));
        $this->assertInstanceOf('CachingIterator', $c->getCachingIterator());
    }

    public function testFilter()
    {
        $foo = (new Text('foo'))->label('Hello');
        $bar = (new Text('bar'))->label('World');

        $c = new FieldCollection(array($foo, $bar));
        $this->assertSame(array('bar' => $bar), $c->filter(function($item) {
                return $item->label() == 'World';
            })->all());
    }

    public function testValues()
    {
        $foo = (new Text('foo'))->label('Hello');
        $bar = (new Text('bar'))->label('World');

        $c = new FieldCollection(array($foo, $bar));

        $this->assertSame(array($bar), $c->filter(function($item) {
                return $item->label() == 'World';
            })->values()->all());
    }

    public function testFlatten()
    {
        // unimplemented, we default to values()

        $foo = (new Text('foo'));
        $bar = (new Text('bar'));

        $c = new FieldCollection(array($foo, $bar));

        $this->assertSame(array($foo, $bar), $c->flatten()->all());
    }

    public function testMergeArray()
    {
        $foo = (new Text('foo'));
        $bar = (new Text('bar'));
        $c = new FieldCollection(array($foo));
        $this->assertSame(array('foo' => $foo, 'bar' => $bar), $c->merge(array($bar))->all());
    }

    public function testMergeCollection()
    {
        $foo = (new Text('foo'));
        $foo2 = (new Text('foo'));
        $bar = (new Text('bar'));
        $c = new FieldCollection(array($foo));
        $this->assertSame(array('foo' => $foo2, 'bar' => $bar), $c->merge(new FieldCollection(array($foo2, $bar)))->all());
    }

    public function testDiffCollection()
    {
        $foo = (new Text('foo'));
        $bar = (new Text('bar'));
        $baz = (new Text('baz'));
        $c = new FieldCollection(array($foo, $bar));
        $this->assertSame(array('foo' => $foo), $c->diff(new FieldCollection(array($bar, $baz)))->all());
    }

    public function testIntersectCollection()
    {
        $foo = (new Text('foo'));
        $bar = (new Text('bar'));
        $baz = (new Text('baz'));
        $c = new FieldCollection(array($foo, $bar));
        $this->assertSame(array('bar' => $bar), $c->intersect(new FieldCollection(array($bar, $baz)))->all());
    }

    public function testUnique()
    {
        $foo = (new Text('foo'));
        $bar = (new Text('bar'));
        $c = new FieldCollection(array($foo, $bar, $bar));
        $this->assertSame(array('foo' => $foo, 'bar' => $bar), $c->unique()->all());
    }

    public function testCollapse()
    {
        $foo = (new Text('foo'));
        $bar = (new Text('bar'));
        $c = new FieldCollection(array($foo, $bar, $bar));
        $this->assertSame(array('foo' => $foo, 'bar' => $bar), $c->collapse()->all());
    }

    public function testSort()
    {
        $foo = (new Text('foo'))->label('Hello');
        $bar = (new Text('bar'))->label('World');
        $c = new FieldCollection(array($foo, $bar));
        $c->sort(function($a, $b) {
            if ($a->name() === $b->name()) {
                return 0;
            }
            return ($a->name() < $b->name()) ? -1 : 1;
        });

        $this->assertSame(array('bar' => $bar, 'foo' => $foo), $c->all());
    }

    public function testSortBy()
    {
        $foo = (new Text('foo'))->label('Hello');
        $bar = (new Text('bar'))->label('World');

        $data = new FieldCollection(array($foo, $bar));
        $data = $data->sortBy(function($x) {
            return $x;
        });

        $this->assertSame(array('bar' => $bar, 'foo' => $foo), $data->all());

        $data = new FieldCollection(array($foo, $bar));
        $data->sortByDesc(function($x) {
            return $x;
        });

        $this->assertSame(array('foo' => $foo, 'bar' => $bar), $data->all());
    }

    public function testSortByString()
    {
        $foo = (new Text('foo'))->label('Hello');
        $bar = (new Text('bar'))->label('World');
        $data = new FieldCollection(array($foo, $bar));
        $data = $data->sortBy('name');

        $this->assertSame(array('bar' => $bar, 'foo' => $foo), $data->all());
    }

    public function testReverse()
    {
        $foo = (new Text('foo'))->label('Hello');
        $bar = (new Text('bar'))->label('World');
        $data = new FieldCollection(array($foo, $bar));
        $reversed = $data->reverse();

        $this->assertSame(array('bar' => $bar, 'foo' => $foo), $reversed->all());
    }

//
//
//	public function testFlip()
//	{
//		$data = new FieldCollection(array('name' => 'taylor', 'framework' => 'laravel'));
//		$this->assertEquals(array('taylor' => 'name', 'laravel' => 'framework'), $data->flip()->toArray());
//	}
//
//
//	public function testChunk ()
//	{
//		$data = new FieldCollection(array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10));
//		$data = $data->chunk(3);
//
//		$this->assertInstanceOf('Tacone\Coffee\Collection\FieldCollection', $data);
//		$this->assertInstanceOf('Tacone\Coffee\Collection\FieldCollection', $data[0]);
//		$this->assertEquals(4, $data->count());
//		$this->assertEquals(array(1, 2, 3), $data[0]->toArray());
//		$this->assertEquals(array(10), $data[3]->toArray());
//	}
//
//
//	public function testListsWithArrayAndObjectValues()
//	{
//		$data = new FieldCollection(array((object) array('name' => 'taylor', 'email' => new Text('foo')), array('name' => 'dayle', 'email' => new Text('bar'))));
//		$this->assertEquals(array('taylor' => new Text('foo'), 'dayle' => new Text('bar')), $data->lists('email', 'name'));
//		$this->assertEquals(array(new Text('foo'), new Text('bar')), $data->lists('email'));
//	}
//
//
//	public function testImplode()
//	{
//		$data = new FieldCollection(array(array('name' => 'taylor', 'email' => new Text('foo')), array('name' => 'dayle', 'email' => new Text('bar'))));
//		$this->assertEquals('foobar', $data->implode('email'));
//		$this->assertEquals('foo,bar', $data->implode('email', ','));
//	}
//
//
//	public function testTake()
//	{
//		$data = new FieldCollection(array('taylor', 'dayle', 'shawn'));
//		$data = $data->take(2);
//		$this->assertEquals(array('taylor', 'dayle'), $data->all());
//	}
//
//
//	public function testRandom()
//	{
//		$data = new FieldCollection(array(1, 2, 3, 4, 5, 6));
//		$random = $data->random();
//		$this->assertInternalType('integer', $random);
//		$this->assertContains($random, $data->all());
//		$random = $data->random(3);
//		$this->assertCount(3, $random);
//	}
//
//
//	public function testTakeLast()
//	{
//		$data = new FieldCollection(array('taylor', 'dayle', 'shawn'));
//		$data = $data->take(-2);
//		$this->assertEquals(array('dayle', 'shawn'), $data->all());
//	}
//
//
//	public function testTakeAll()
//	{
//		$data = new FieldCollection(array('taylor', 'dayle', 'shawn'));
//		$data = $data->take();
//		$this->assertEquals(array('taylor', 'dayle', 'shawn'), $data->all());
//	}
//
//
//	public function testMakeMethod()
//	{
//		$collection = Collection::make(new Text('foo'));
//		$this->assertEquals(array(new Text('foo')), $collection->all());
//	}
//
//
//	public function testSplice()
//	{
//		$data = new FieldCollection(array(new Text('foo'), 'baz'));
//		$data->splice(1, 0, new Text('bar'));
//		$this->assertEquals(array(new Text('foo'), new Text('bar'), 'baz'), $data->all());
//
//		$data = new FieldCollection(array(new Text('foo'), 'baz'));
//		$data->splice(1, 1);
//		$this->assertEquals(array(new Text('foo')), $data->all());
//
//		$data = new FieldCollection(array(new Text('foo'), 'baz'));
//		$cut = $data->splice(1, 1, new Text('bar'));
//		$this->assertEquals(array(new Text('foo'), new Text('bar')), $data->all());
//		$this->assertEquals(array('baz'), $cut->all());
//	}
//
//
//	public function testGetListValueWithAccessors()
//	{
//		$model    = new TestAccessorEloquentTestStub(array('some' => new Text('foo')));
//		$modelTwo = new TestAccessorEloquentTestStub(array('some' => new Text('bar')));
//		$data     = new FieldCollection(array($model, $modelTwo));
//
//		$this->assertEquals(array(new Text('foo'), new Text('bar')), $data->lists('some'));
//	}
//
//
//	public function testTransform()
//	{
//		$data = new FieldCollection(array('taylor', 'colin', 'shawn'));
//		$data->transform(function($item) { return strrev($item); });
//		$this->assertEquals(array('rolyat', 'niloc', 'nwahs'), array_values($data->all()));
//	}
//
//
//	public function testFirstWithCallback()
//	{
//		$data = new FieldCollection(array(new Text('foo'), new Text('bar'), 'baz'));
//		$result = $data->first(function($key, $value) { return $value === new Text('bar'); });
//		$this->assertEquals(new Text('bar'), $result);
//	}
//
//
//	public function testFirstWithCallbackAndDefault()
//	{
//		$data = new FieldCollection(array(new Text('foo'), new Text('bar')));
//		$result = $data->first(function($key, $value) { return $value === 'baz'; }, 'default');
//		$this->assertEquals('default', $result);
//	}
//
//
//	public function testGroupByAttribute()
//	{
//		$data = new FieldCollection(array(array('rating' => 1, 'name' => '1'), array('rating' => 1, 'name' => '2'), array('rating' => 2, 'name' => '3')));
//		$result = $data->groupBy('rating');
//		$this->assertEquals(array(1 => array(array('rating' => 1, 'name' => '1'), array('rating' => 1, 'name' => '2')), 2 => array(array('rating' => 2, 'name' => '3'))), $result->toArray());
//	}
//
//
//	public function testKeyByAttribute()
//	{
//		$data = new FieldCollection([['rating' => 1, 'name' => '1'], ['rating' => 2, 'name' => '2'], ['rating' => 3, 'name' => '3']]);
//		$result = $data->keyBy('rating');
//		$this->assertEquals([1 => ['rating' => 1, 'name' => '1'], 2 => ['rating' => 2, 'name' => '2'], 3 => ['rating' => 3, 'name' => '3']], $result->all());
//	}
//
//
//	public function testContains()
//	{
//		$c = new FieldCollection([1, 3, 5]);
//
//		$this->assertEquals(true,  $c->contains(1));
//		$this->assertEquals(false, $c->contains(2));
//		$this->assertEquals(true,  $c->contains(function($value) { return $value < 5; }));
//		$this->assertEquals(false, $c->contains(function($value) { return $value > 5; }));
//	}
//
//
//	public function testGettingSumFromCollection()
//	{
//		$c = new FieldCollection(array((object) array(new Text('foo') => 50), (object) array(new Text('foo') => 50)));
//		$this->assertEquals(100, $c->sum(new Text('foo')));
//
//		$c = new FieldCollection(array((object) array(new Text('foo') => 50), (object) array(new Text('foo') => 50)));
//		$this->assertEquals(100, $c->sum(function($i) { return $i->foo; }));
//	}
//
//
//	public function testGettingSumFromEmptyCollection()
//	{
//		$c = new FieldCollection();
//		$this->assertEquals(0, $c->sum(new Text('foo')));
//	}
//
//
//	public function testValueRetrieverAcceptsDotNotation()
//	{
//		$c = new FieldCollection(array(
//			(object) array('id' => 1, new Text('foo') => array(new Text('bar') => 'B')), (object) array('id' => 2, new Text('foo') => array(new Text('bar') => 'A'))
//		));
//
//		$c = $c->sortBy('foo.bar');
//		$this->assertEquals(array(2, 1), $c->lists('id'));
//	}
//
//
//	public function testPullRetrievesItemFromCollection()
//	{
//		$c = new FieldCollection(array(new Text('foo'), new Text('bar')));
//
//		$this->assertEquals(new Text('foo'), $c->pull(0));
//	}
//
//
//	public function testPullRemovesItemFromCollection()
//	{
//		$c = new FieldCollection(array(new Text('foo'), new Text('bar')));
//		$c->pull(0);
//		$this->assertEquals(array(1 => new Text('bar')), $c->all());
//	}
//
//
//	public function testPullReturnsDefault()
//	{
//		$c = new FieldCollection(array());
//		$value = $c->pull(0, new Text('foo'));
//		$this->assertEquals(new Text('foo'), $value);
//	}
//
//
//	public function testRejectRemovesElementsPassingTruthTest()
//	{
//		$c = new FieldCollection([new Text('foo'), new Text('bar')]);
//		$this->assertEquals([new Text('foo')], $c->reject(new Text('bar'))->values()->all());
//
//		$c = new FieldCollection([new Text('foo'), new Text('bar')]);
//		$this->assertEquals([new Text('foo')], $c->reject(function($v) { return $v == new Text('bar'); })->values()->all());
//
//		$c = new FieldCollection([new Text('foo'), null]);
//		$this->assertEquals([new Text('foo')], $c->reject(null)->values()->all());
//
//		$c = new FieldCollection([new Text('foo'), new Text('bar')]);
//		$this->assertEquals([new Text('foo'), new Text('bar')], $c->reject('baz')->values()->all());
//
//		$c = new FieldCollection([new Text('foo'), new Text('bar')]);
//		$this->assertEquals([new Text('foo'), new Text('bar')], $c->reject(function($v) { return $v == 'baz'; })->values()->all());
//	}
//
//
//	public function testKeys()
//	{
//		$c = new FieldCollection(array('name' => 'taylor', 'framework' => 'laravel'));
//		$this->assertEquals(array('name', 'framework'), $c->keys());
//	}
//
//}
//
//class TestAccessorEloquentTestStub
//{
//	protected $attributes = array();
//
//	public function __construct($attributes)
//	{
//		$this->attributes = $attributes;
//	}
//
//
//	public function __get($attribute)
//	{
//		$accessor = 'get' .lcfirst($attribute). 'Attribute';
//		if (method_exists($this, $accessor)) {
//			return $this->$accessor();
//		}
//
//		return $this->$attribute;
//	}
//
//
//	public function getSomeAttribute()
//	{
//		return $this->attributes['some'];
//	}
}
