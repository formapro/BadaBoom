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
    public function shouldAllowToFilter()
    {
        $filter = $this->createMockChainFilter();

        $filter->filter(new \Exception());
    }

    /**
     *
     * @test
     */
    public function shouldCallFilterIfDataContainException()
    {
        $data = new DataHolder();
        $data->set('exception', new \Exception('foo'));

        $filter = $this->createMockChainFilter();
        $filter->expects($this->once())->method('filter');

        $filter->handle($data);
    }

    /**
     *
     * @test
     */
    public function shouldProxyDataFromHandleToFilter()
    {
        $e = new \Exception('foo');

        $data = new DataHolder();
        $data->set('exception', $e);

        $filter = $this->createMockChainFilter();
        $filter->expects($this->once())->method('filter')->with($this->equalTo($e));

        $filter->handle($data);
    }

    /**
     *
     * @test
     */
    public function shouldNotCallFilterIfDataContainException()
    {
        $filter = $this->createMockChainFilter();
        $filter->expects($this->never())->method('filter');

        $filter->handle(new DataHolder());
    }

    /**
     *
     * @test
     */
    public function shouldHandleNextNodeIfFiltrationPassed()
    {
        $data = new DataHolder();
        $data->set('exception', new \Exception('foo'));
        
        $nextNode = $this->createMockChainNode();
        $nextNode->expects($this->once())->method('handle');

        $filter = $this->createMockChainFilter();
        $filter->expects($this->atLeastOnce())->method('filter')->will($this->returnValue(true));
        $filter->nextNode($nextNode);


        $filter->handle($data);
    }

    /**
     *
     * @test
     */
    public function shouldNotHandleNextNodeIfFiltrationNotPassed()
    {
        $data = new DataHolder();
        $data->set('exception', new \Exception('foo'));

        $nextNode = $this->createMockChainNode();
        $nextNode->expects($this->never())->method('handle');

        $filter = $this->createMockChainFilter();
        $filter->expects($this->atLeastOnce())->method('filter')->will($this->returnValue(false));
        $filter->nextNode($nextNode);

        $filter->handle($data);
    }

    protected function createMockChainFilter()
    {
        return $this->getMockForAbstractClass('BadaBoom\ChainNode\Filter\AbstractFilterChainNode');
    }

    protected function createMockChainNode()
    {
        return $this->getMockForAbstractClass('BadaBoom\ChainNode\AbstractChainNode');
    }
}