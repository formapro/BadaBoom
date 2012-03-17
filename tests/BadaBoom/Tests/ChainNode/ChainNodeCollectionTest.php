<?php

namespace BadaBoom\Tests\ChainNode;

use BadaBoom\ChainNode\CallbackChainNode;
use BadaBoom\ChainNode\ChainNodeCollection;
use BadaBoom\DataHolder\DataHolder;

class ChainNodeCollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldImplementChainNodeCollectionInterface()
    {
        $rc = new \ReflectionClass('BadaBoom\ChainNode\ChainNodeCollection');
        $this->assertTrue($rc->implementsInterface('BadaBoom\ChainNode\ChainNodeCollectionInterface'));
    }

    /**
     * @test
     */
    public function couldBeConstructedWithoutArguments()
    {
        new ChainNodeCollection;
    }

    /**
     * @test
     */
    public function shouldAppend()
    {
        $collection = new ChainNodeCollection();
        $collection->append($this->createChainNodeMock());
    }

    /**
     * @test
     */
    public function shouldPrepend()
    {
        $collection = new ChainNodeCollection();
        $collection->prepend($this->createChainNodeMock());
    }

    /**
     * @test
     */
    public function shouldPrependAndAppend()
    {
        $collection = new ChainNodeCollection();

        $firstNode = $this->createChainNodeMock();
        $secondNode = $this->createChainNodeMock();
        $thirdNode = $this->createChainNodeMock();
        $fourthNode = $this->createChainNodeMock();

        $collection->append($thirdNode);
        $collection->prepend($secondNode);
        $collection->append($fourthNode);
        $collection->prepend($firstNode);
    }

    /**
     * @test
     */
    public function shouldHandleEmptyCollection()
    {
        $collection = new ChainNodeCollection();
        $collection->handle(new \Exception(), new DataHolder());
    }

    /**
     * @test
     */
    public function shouldAllowToSetNextNode()
    {
        $collection = new ChainNodeCollection();
        $collection->nextNode($this->createChainNodeMock());
    }

    /**
     * @test
     */
    public function shouldHandleEmptyCollectionAndDelegateHandlingToNextNode()
    {
        $exception = new \Exception();
        $data = new DataHolder();
        $collection = new ChainNodeCollection();
        $nextNode = $this->createChainNodeMock();

        $collection->nextNode($nextNode);

        $nextNode
            ->expects($this->once())
            ->method('handle')
            ->with($exception, $data)
        ;

        $collection->handle($exception, $data);
    }

    /**
     * @test
     */
    public function shouldExecuteAppendedNodes()
    {
        $collection = new ChainNodeCollection();

        $firstNode = new FailSafeChainNodeStub();
        $secondNode = new FailSafeChainNodeStub();

        $collection->append($firstNode);
        $collection->append($secondNode);

        $collection->handle(new \Exception(), new DataHolder());

        $this->assertTrue($firstNode->isCalled);
        $this->assertTrue($secondNode->isCalled);
    }

    /**
     * @test
     */
    public function shouldExecutePrependedNodes()
    {
        $collection = new ChainNodeCollection();

        $firstNode  = new FailSafeChainNodeStub();
        $secondNode = new FailSafeChainNodeStub();

        $collection->prepend($firstNode);
        $collection->prepend($secondNode);

        $collection->handle(new \Exception(), new DataHolder());

        $this->assertTrue($firstNode->isCalled);
        $this->assertTrue($secondNode->isCalled);
    }

    /**
     * @test
     */
    public function shouldExecuteAddedNodes()
    {
        $collection = new ChainNodeCollection();

        $firstNode  = new FailSafeChainNodeStub();
        $secondNode = new FailSafeChainNodeStub();
        $thirdNode  = new FailSafeChainNodeStub();
        $fourthNode = new FailSafeChainNodeStub();

        $collection->append($firstNode);
        $collection->prepend($secondNode);
        $collection->prepend($thirdNode);
        $collection->append($fourthNode);

        $collection->handle(new \Exception(), new DataHolder());

        $this->assertTrue($firstNode->isCalled);
        $this->assertTrue($secondNode->isCalled);
        $this->assertTrue($thirdNode->isCalled);
        $this->assertTrue($fourthNode->isCalled);
    }

    /**
     * @test
     */
    public function shouldBuildChainInRightOrder()
    {
        $exception  = new \Exception();
        $data       = new DataHolder();
        $collection = new ChainNodeCollection();

        $firstNode  = $this->createChainNodeMock();
        $secondNode = $this->createChainNodeMock();
        $thirdNode  = $this->createChainNodeMock();

        $nextNode   = $this->createChainNodeMock();

        $collection->prepend($firstNode);
        $collection->append($secondNode);
        $collection->prepend($thirdNode);

        $collection->nextNode($nextNode);

        $firstNode
            ->expects($this->once())
            ->method('nextNode')
            ->with($this->equalTo($secondNode))
        ;

        $thirdNode
            ->expects($this->once())
            ->method('nextNode')
            ->with($this->equalTo($firstNode))
        ;

        $secondNode
            ->expects($this->once())
            ->method('nextNode')
            ->with($this->equalTo($nextNode))
        ;

        $thirdNode
            ->expects($this->once())
            ->method('handle')
            ->with($exception, $data)
        ;

        $collection->handle($exception, $data);
    }

    /**
     * @test
     */
    public function shouldInterruptHandlingIfAddedNodeDoesNotDelegateProcessing()
    {
        $collection = new ChainNodeCollection();

        $firstNode  = new FailSafeChainNodeStub();
        $secondNode = new FailSafeChainNodeStub();
        $thirdNode  = new DisastrousChainNodeStub();
        $fourthNode = new FailSafeChainNodeStub();

        $collection->prepend($firstNode);
        $collection->prepend($secondNode);
        $collection->append($thirdNode);
        $collection->append($fourthNode);

        $collection->handle(new \Exception(), new DataHolder());

        $this->assertTrue($firstNode->isCalled);
        $this->assertTrue($secondNode->isCalled);
        $this->assertFalse($fourthNode->isCalled);
    }

    /**
     * @test
     */
    public function shouldNotDelegateToNextNodeIfChainWasInterrupted()
    {
        $collection = new ChainNodeCollection();

        $firstNode  = new FailSafeChainNodeStub();
        $secondNode  = new DisastrousChainNodeStub();

        $collection->prepend($firstNode);
        $collection->append($secondNode);

        $nextNode = new FailSafeChainNodeStub();
        $collection->nextNode($nextNode);

        $collection->handle(new \Exception(), new DataHolder());

        $this->assertTrue($firstNode->isCalled);
        $this->assertFalse($nextNode->isCalled);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function createChainNodeMock()
    {
        return $this->getMock('BadaBoom\ChainNode\ChainNodeInterface');
    }
}


use BadaBoom\ChainNode\AbstractChainNode;
use BadaBoom\DataHolder\DataHolderInterface;

class FailSafeChainNodeStub extends AbstractChainNode
{
    /**
     * @var boolean
     */
    public  $isCalled = false;

    /**
     * {@inheritdoc}
     */
    public function handle(\Exception $exception, DataHolderInterface $data)
    {
        $this->isCalled = true;

        $this->handleNextNode($exception, $data);
    }
}

class DisastrousChainNodeStub extends AbstractChainNode
{
    /**
     * {@inheritdoc}
     */
    public function handle(\Exception $exception, DataHolderInterface $data){}
}

