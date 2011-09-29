<?php

namespace BadaBoom\Tests\ChainNode\Filter;

use BadaBoom\DataHolder\DataHolder;

class AbstractFilterChainNodeTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     * @test
     */
    public function shouldNotBeInstanciable()
    {
        $rc = new \ReflectionClass('BadaBoom\ChainNode\Filter\AbstractFilterChainNode');
        $this->assertFalse($rc->isInstantiable());
    }

    /**
     *
     * @test
     */
    public function shouldBeSubclassOfAbstractChainNode()
    {
        $rc = new \ReflectionClass('BadaBoom\ChainNode\Filter\AbstractFilterChainNode');
        $this->assertTrue($rc->isSubclassOf('BadaBoom\ChainNode\AbstractChainNode'));
    }

    /**
     *
     * @test
     */
    public function shouldAllowToFilter()
    {
        $filter = $this->createMockFilter();

        $filter->filter(new \Exception, new DataHolder);
    }

    /**
     *
     * @test
     */
    public function shouldProxyDataFromHandleToFilter()
    {
        $e = new \Exception('foo');
        $data = new DataHolder();

        $filter = $this->createMockFilter();
        $filter->expects($this->once())->method('filter')->with($this->equalTo($e), $this->equalTo($data));

        $filter->handle($e, $data);
    }

    /**
     *
     * @test
     */
    public function shouldHandleNextNodeIfFiltrationPassed()
    {
        $e = new \Exception('foo');
        $data = new DataHolder();

        $nextNode = $this->createMockChainNode();
        $nextNode->expects($this->once())->method('handle');

        $filter = $this->createMockFilter();
        $filter->expects($this->atLeastOnce())->method('filter')->will($this->returnValue(true));
        $filter->nextNode($nextNode);

        $filter->handle($e, $data);
    }

    /**
     *
     * @test
     */
    public function shouldHandleNextNodeAndPassExceptionAndHolderToIt()
    {
        $e = new \Exception('foo');
        $data = new DataHolder();

        $nextNode = $this->createMockChainNode();
        $nextNode->expects($this->once())->method('handle')->with($this->equalTo($e), $this->equalTo($data));

        $filter = $this->createMockFilter();
        $filter->expects($this->atLeastOnce())->method('filter')->will($this->returnValue(true));
        $filter->nextNode($nextNode);

        $filter->handle($e, $data);
    }

    /**
     *
     * @test
     */
    public function shouldNotHandleNextNodeIfFiltrationNotPassed()
    {
        $e = new \Exception('foo');
        $data = new DataHolder();

        $nextNode = $this->createMockChainNode();
        $nextNode->expects($this->never())->method('handle');

        $filter = $this->createMockFilter();
        $filter->expects($this->atLeastOnce())->method('filter')->will($this->returnValue(false));
        $filter->nextNode($nextNode);

        $filter->handle($e, $data);
    }

    protected function createMockFilter()
    {
        return $this->getMockForAbstractClass('BadaBoom\ChainNode\Filter\AbstractFilterChainNode');
    }

    protected function createMockChainNode()
    {
        return $this->getMockForAbstractClass('BadaBoom\ChainNode\AbstractChainNode');
    }
}