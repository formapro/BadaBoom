<?php

namespace BadaBoom\Tests\ChainNode\Filter;

use BadaBoom\ChainNode\Filter\ExceptionClassFilter;
use BadaBoom\DataHolder\DataHolder;

class ExceptionClassFilterTest extends \PHPUnit_Framework_TestCase
{
    public static function provideFilterCases()
    {
        return array(
            array('Exception', true, 'Should allow if rule defined for exactly this class'),
            array('LogicException', false, 'Should deny if rule defined for exactly this class'),
            array('RuntimeException', true, 'Should allow if rule defined for parent class'),
            array('BadMethodCallException', false, 'Should deny if rule defined for parent class'),
        );
    }

    /**
     *
     * @test
     */
    public function shouldImplementChainNodeInterface()
    {
        $rc = new \ReflectionClass('BadaBoom\ChainNode\Filter\ExceptionClassFilter');
        $this->assertTrue($rc->isSubclassOf('BadaBoom\ChainNode\Filter\AbstractFilterChainNode'));
    }

    /**
     *
     * @test
     */
    public function shouldAllowToSetAllowedClasses()
    {
        $filter = new ExceptionClassFilter();

        $filter->allow('Exception');
    }

    /**
     *
     * @test
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Class not exists: `NotExistException`
     */
    public function shouldThrowIfAllowedClassNotExist()
    {
        $filter = new ExceptionClassFilter();

        $filter->allow('NotExistException');
    }

    /**
     *
     * @test
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Class `stdClass` is not a subclass of `Exception`
     */
    public function shouldThrowIfAllowedClassIsNotSubclassOfException()
    {
        $filter = new ExceptionClassFilter();

        $filter->allow('stdClass');
    }

    /**
     *
     * @test
     */
    public function shouldAllowToSetDeniedClasses()
    {
        $filter = new ExceptionClassFilter();

        $filter->deny('Exception');
    }

    /**
     *
     * @test
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Class not exists: `NotExistException`
     */
    public function shouldThrowIfDeniedClassNotExist()
    {
        $filter = new ExceptionClassFilter();

        $filter->deny('NotExistException');
    }

    /**
     *
     * @test
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Class `stdClass` is not a subclass of `Exception`
     */
    public function shouldThrowIfDeniedClassIsNotSubclassOfException()
    {
        $filter = new ExceptionClassFilter();

        $filter->deny('stdClass');
    }

    /**
     *
     * @test
     */
    public function shouldDenyByDefault()
    {
        $data = new DataHolder();
        $data->set('exception', new \Exception);

        $filter = new ExceptionClassFilter();

        $this->assertFalse($filter->filter($data));
    }

    /**
     *
     * @test
     *
     * @dataProvider provideFilterCases
     */
    public function shouldWorkAsExpected($exceptionClass, $expectedResult, $failMessage)
    {
        $data = new DataHolder();
        $data->set('exception', new $exceptionClass);

        $filter = new ExceptionClassFilter();

        $filter->allow('Exception');
        $filter->deny('LogicException');
        $filter->allow('InvalidArgumentException');
        $filter->deny('BadFunctionCallException');

        $this->assertEquals($expectedResult, $filter->filter($data), $failMessage);
    }

    /**
     *
     * @test
     */
    public function shouldRewriteRule()
    {
        $data = new DataHolder();
        $data->set('exception', new \Exception);

        $filter = new ExceptionClassFilter();

        $filter->deny('Exception');
        $filter->allow('Exception');

        $this->assertTrue($filter->filter($data));
    }
}