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

        $filter->filter(new DataHolder);
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
        $data = new DataHolder();
        $data->set('exception', new \Exception('foo'));

        $filter = $this->createMockChainFilter();
        $filter->expects($this->once())->method('filter')->with($this->equalTo($data));

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
    public function shouldHandleNextNodeIfFilterationPassed()
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
    public function shouldNotHandleNextNodeIfFilterationNotPassed()
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