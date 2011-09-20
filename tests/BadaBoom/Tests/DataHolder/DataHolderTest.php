<?php

namespace BadaBoom\Tests\DataHolder;

use BadaBoom\DataHolder\DataHolder;

class DataHolderTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     * @test
     */
    public function shouldImplementDataHolderInterface()
    {
        $rc = new \ReflectionClass('BadaBoom\DataHolder\DataHolder');
        $this->assertTrue($rc->implementsInterface('BadaBoom\DataHolder\DataHolderInterface'));
    }

    /**
     *
     * @test
     *
     * @depends shouldImplementDataHolderInterface
     */
    public function shouldAllowToSetValueWithName()
    {
        $holder = new DataHolder();

        $holder->set('foo', 'bar');
    }

    /**
     *
     * @test
     *
     * @depends shouldImplementDataHolderInterface
     */
    public function shouldAllowToFindOutWhetherValueSetOrNot()
    {
        $holder = new DataHolder();

        $this->assertFalse($holder->has('foo'));
        
        $holder->set('foo', 'bar');

        $this->assertTrue($holder->has('foo'));
    }

    /**
     *
     * @test
     *
     * @depends shouldAllowToSetValueWithName
     */
    public function shouldAllowToGetPreviouslySetValueByName()
    {
        $holder = new DataHolder();

        $holder->set('foo', 'bar');

        $this->assertEquals( 'bar', $holder->get('foo'));
    }

    /**
     * @test
     */
    public function shouldAllowToGetDefaultValueIfValueWasNotSetBefore()
    {
        $varName = 'foo';
        $defaultValue = 'My best default value';
        $holder = new DataHolder();

        $this->assertFalse($holder->has($varName));
        $this->assertEquals($defaultValue, $holder->get($varName, $defaultValue));
    }

    /**
     *
     * @test
     */
    public function shouldReturnNullIfValueWasNotSetAndDefaultWasNotGiven()
    {
        $holder = new DataHolder();

        $this->assertNull($holder->get('foo'));
    }

    /**
     *
     * @test
     */
    public function shouldImplementIteratorAggregateInterface()
    {
        $holder = new DataHolder();
        $this->assertInstanceOf('IteratorAggregate', $holder);

        $iterator = $holder->getIterator();
        $this->assertInstanceOf('Iterator', $iterator);
    }
}
