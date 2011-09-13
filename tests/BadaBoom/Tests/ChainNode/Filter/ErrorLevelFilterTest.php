<?php

namespace BadaBoom\Tests\ChainNode\Filter;

use BadaBoom\DataHolder\DataHolder;
use BadaBoom\ChainNode\Filter\ErrorLevelFilter;

class ErrorLevelFilterTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     * @test
     */
    public function shouldImplementChainNodeInterface()
    {
        $rc = new \ReflectionClass('BadaBoom\ChainNode\Filter\ErrorLevelFilter');
        $this->assertTrue($rc->isSubclassOf('BadaBoom\ChainNode\Filter\AbstractFilterChainNode'));
    }

    /**
     *
     * @test
     */
    public function shouldCallDirectlyNextNotWithoutFilteringIfDataContainNoErrorException()
    {
        $exception = new \Exception('foo');
        $data = new DataHolder;
        $data->set('exception', $exception);

        $nextNode = $this->getMockForAbstractClass('BadaBoom\ChainNode\AbstractChainNode');
        $nextNode->expects($this->once())->method('handle')->with($this->equalTo($data));

        $filter = $this->getMock('BadaBoom\ChainNode\Filter\ErrorLevelFilter', array('filter'));
        $filter->expects($this->never())->method('filter');
        $filter->nextNode($nextNode);


        $filter->handle($data);
    }

    /**
     *
     * @test
     */
    public function shouldCallFilterIfDataContainErrorException()
    {
        $exception = new \ErrorException('foo');
        $data = new DataHolder;
        $data->set('exception', $exception);

        $filter = $this->getMock('BadaBoom\ChainNode\Filter\ErrorLevelFilter', array('filter'));
        $filter->expects($this->once())->method('filter');

        $filter->handle($data);
    }

    /**
     *
     * @test
     */
    public function shouldPassIfNoRulesDefined()
    {
        $exception = new \ErrorException('foo');
        $data = new DataHolder;
        $data->set('exception', $exception);

        $nextNode = $this->getMockForAbstractClass('BadaBoom\ChainNode\AbstractChainNode');
        $nextNode->expects($this->once())->method('handle')->with($this->equalTo($data));

        $filter = new ErrorLevelFilter();
        $filter->nextNode($nextNode);

        $filter->handle($data);
    }

    /**
     * 
     * @test
     */
    public function shouldAllowToDefineDeniedErrors()
    {
        $filter = new ErrorLevelFilter();
        $filter->deny(E_WARNING);
    }

    /**
     *
     * @test
     */
    public function shouldFilterDeniedErrors()
    {
        $e = new \ErrorException('foo', null, E_WARNING);

        $filter = new ErrorLevelFilter();
        $filter->deny(E_WARNING);

        $this->assertFalse($filter->filter($e));
    }
}