<?php
namespace BadaBoom\Tests\ChainNode\Filter;

use BadaBoom\DataHolder\DataHolder;
use BadaBoom\ChainNode\Filter\ErrorLevelFilter;

class ErrorLevelFilterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldBeSubClassOfAbstractFilter()
    {
        $rc = new \ReflectionClass('BadaBoom\ChainNode\Filter\ErrorLevelFilter');
        $this->assertTrue($rc->isSubclassOf('BadaBoom\ChainNode\Filter\AbstractFilter'));
    }

    /**
     * @test
     */
    public function shouldAllowToDefineDeniedErrors()
    {
        $filter = new ErrorLevelFilter();

        $filter->deny(E_WARNING);
    }

    /**
     * @test
     */
    public function shouldAlwaysPropagateNoErrorException()
    {
        $exception = new \Exception('foo');
        $data = new DataHolder;

        $filter = new ErrorLevelFilter();

        $filter->deny(E_ALL);

        $this->assertTrue($filter->shouldContinue($exception, $data));
    }

    /**
     * @test
     */
    public function shouldPropagateIfNoRulesDefined()
    {
        $exception = new \ErrorException('foo', null, E_NOTICE, 'foo', '123');
        $data = new DataHolder;

        $filter = new ErrorLevelFilter();

        $this->assertTrue($filter->shouldContinue($exception, $data));
    }

    /**
     * @test
     */
    public function shouldFilterDeniedErrors()
    {
        $exception = new \ErrorException('foo', null, E_WARNING);
        $data = new DataHolder;

        $filter = new ErrorLevelFilter();
        $filter->deny(E_WARNING);

        $this->assertFalse($filter->shouldContinue($exception, $data));
    }
}