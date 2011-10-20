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
    public function shouldBeSubClassOfAbstractFilter()
    {
        $rc = new \ReflectionClass('BadaBoom\ChainNode\Filter\ExceptionClassFilter');
        $this->assertTrue($rc->isSubclassOf('BadaBoom\ChainNode\Filter\AbstractFilter'));
    }

    /**
     *
     * @test
     */
    public function shouldAllowToSetAllowedExceptionClass()
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
    public function shouldThrowIfAllowedExceptionClassNotExist()
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
    public function shouldThrowIfAllowedExceptionClassIsNotSubclassOfException()
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
    public function shouldThrowIfDeniedExceptionClassNotExist()
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
    public function shouldThrowIfDeniedExceptionClassIsNotSubclassOfException()
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
        $filter = new ExceptionClassFilter();

        $this->assertFalse($filter->filter(new \Exception, new DataHolder));
    }

    /**
     *
     * @test
     *
     * @dataProvider provideFilterCases
     */
    public function shouldWorkAsExpectedInCases($exceptionClass, $expectedResult, $failMessage)
    {
        $filter = new ExceptionClassFilter();

        $filter->allow('Exception');
        $filter->deny('LogicException');
        $filter->allow('InvalidArgumentException');
        $filter->deny('BadFunctionCallException');

        //SUT
        $result = $filter->filter(new $exceptionClass, new DataHolder());

        $this->assertEquals($expectedResult, $result, $failMessage);
    }

    /**
     *
     * @test
     *
     * @dataProvider provideFilterCases
     */
    public function shouldNotDependsOnRulesOrder($exceptionClass, $expectedResult, $failMessage)
    {
        $filter = new ExceptionClassFilter();

        $filter->deny('BadFunctionCallException');
        $filter->allow('InvalidArgumentException');
        $filter->deny('LogicException');
        $filter->allow('Exception');

        //SUT
        $result = $filter->filter(new $exceptionClass, new DataHolder());

        $this->assertEquals($expectedResult, $result, $failMessage);
    }

    /**
     *
     * @test
     */
    public function shouldRewriteRule()
    {
        $filter = new ExceptionClassFilter();

        $filter->deny('Exception');
        $filter->allow('Exception');

        $this->assertTrue($filter->filter(new \Exception, new DataHolder));
    }

    protected function createMockChainNode()
    {
        return $this->getMockForAbstractClass('BadaBoom\ChainNode\AbstractChainNode');
    }
}