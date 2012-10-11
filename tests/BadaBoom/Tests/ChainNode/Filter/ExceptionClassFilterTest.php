<?php
namespace BadaBoom\Tests\ChainNode\Filter;

use BadaBoom\ChainNode\Filter\ExceptionClassFilter;
use BadaBoom\Context;

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
     * @test
     */
    public function shouldAllowToSetDeniedClasses()
    {
        $filter = new ExceptionClassFilter();

        $filter->deny('Exception');
    }

    /**
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
     * @test
     */
    public function shouldAllowPropagationByDefault()
    {
        $filter = new ExceptionClassFilter();

        $this->assertTrue($filter->shouldContinue(new Context(new \Exception)));
    }

    /**
     * @test
     */
    public function shouldAllowDefinePropagateAll()
    {
        $filter = new ExceptionClassFilter();

        $filter->allowAll();

        $this->assertTrue($filter->shouldContinue(new Context(new \Exception)));
    }

    /**
     * @test
     */
    public function shouldAllowDefineNotPropagateAll()
    {
        $filter = new ExceptionClassFilter();

        $filter->denyAll();

        $this->assertFalse($filter->shouldContinue(new Context(new \Exception)));
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
        $result = $filter->shouldContinue(new Context(new $exceptionClass));

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
        $result = $filter->shouldContinue(new Context(new $exceptionClass));

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

        $this->assertTrue($filter->shouldContinue(new Context(new \Exception)));
    }

    protected function createMockChainNode()
    {
        return $this->getMockForAbstractClass('BadaBoom\ChainNode\AbstractChainNode');
    }
}