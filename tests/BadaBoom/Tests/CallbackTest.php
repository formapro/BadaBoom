<?php

namespace BadaBoom\Tests;

use BadaBoom\Callback;

class CallbackTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     * @test
     */
    public function shouldTakeChainNodeInConstructor()
    {
        new Callback($this->createMockChainNode());
    }

    /**
     *
     * @test
     */
    public function shouldHandleException()
    {
        $chain = $this->createMockChainNode();
        $callback = new Callback($chain);
        $callback->handleException(new \Exception());
    }

    /**
     *
     * @test
     */
    public function shouldDelegateHandlingToChain()
    {
        $chain = $this->createMockChainNode();
        $chain->expects($this->once())->method('handle');

        $callback = new Callback($chain);

        $callback->handleException(new \Exception());
    }

    /**
     *
     * @test
     */
    public function shouldDelegateHandlingToChainWithExceptionInDataHolder()
    {
        $testCase = $this;
        $exception = new \Exception();
        $chain = $this->createMockChainNode();
        $chain->expects($this->once())
            ->method('handle')
            ->will($this->returnCallback(function($dataHolder) use ($testCase, $exception) {
                $testCase->assertInstanceOf('BadaBoom\DataHolder\DataHolder', $dataHolder);
                $testCase->assertSame($exception, $dataHolder->get('exception'));
            }
        ));

        $callback = new Callback($chain);

        $callback->handleException($exception);
    }

    /**
     *
     * @return BadaBoom\ChainNode\AbstractChainNode
     */
    protected function createMockChainNode()
    {
        return $this->getMockForAbstractClass('BadaBoom\ChainNode\AbstractChainNode');
    }
}