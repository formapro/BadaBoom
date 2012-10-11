<?php
namespace BadaBoom\Tests\ChainNode\Filter;

use BadaBoom\Context;
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
        $context = new Context(new \Exception('foo'));

        $filter = new ErrorLevelFilter();

        $filter->deny(E_ALL);

        $this->assertTrue($filter->shouldContinue($context));
    }

    /**
     * @test
     */
    public function shouldPropagateIfNoRulesDefined()
    {
        $context = new Context(new \ErrorException('foo', null, E_NOTICE, 'foo', '123'));

        $filter = new ErrorLevelFilter();

        $this->assertTrue($filter->shouldContinue($context));
    }

    /**
     * @test
     */
    public function shouldFilterDeniedErrors()
    {
        $context = new Context(new \ErrorException('foo', null, E_WARNING));

        $filter = new ErrorLevelFilter();
        $filter->deny(E_WARNING);

        $this->assertFalse($filter->shouldContinue($context));
    }
}