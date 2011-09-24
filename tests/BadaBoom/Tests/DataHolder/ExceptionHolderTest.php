<?php

namespace BadaBoom\Tests\DataHolder;

use BadaBoom\DataHolder\ExceptionHolder;

class ExceptionHolderTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     * @test
     */
    public function shouldImplementDataHolderInterface()
    {
        $rc = new \ReflectionClass('BadaBoom\DataHolder\ExceptionHolder');
        $this->assertTrue($rc->isSubclassOf('BadaBoom\DataHolder\DataHolder'));
    }

    /**
     * 
     * @test
     */
    public function shouldTakeAnExceptionInTheConstructor()
    {
        new ExceptionHolder(new \Exception('foo'));
    }

    /**
     * 
     * @test
     */
    public function shouldAllowToGetExceptionSetInTheConstructor()
    {
        $e = new \Exception('foo');

        $holder = new ExceptionHolder($e);

        $this->assertSame($e, $holder->getException());
    }
}