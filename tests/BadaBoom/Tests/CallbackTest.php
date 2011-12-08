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

        $callback(new \Exception());
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

        $callback(new \Exception());
    }

    /**
     *
     * @test
     */
    public function shouldDelegateHandlingToChainWithException()
    {
        $testCase = $this;
        $expectedException = new \Exception();

        $chain = $this->createMockChainNode();
        $chain->expects($this->once())
            ->method('handle')
            ->will($this->returnCallback(function($actualException) use ($testCase, $expectedException){
                $testCase->assertSame($expectedException, $actualException);
            }
        ));

        $callback = new Callback($chain);

        $callback($expectedException);
    }

    /**
     *
     * @test
     */
    public function shouldDelegateHandlingToChainWithCreatedDataHolder()
    {
        $testCase = $this;

        $chain = $this->createMockChainNode();
        $chain->expects($this->once())
            ->method('handle')
            ->will($this->returnCallback(function($exception, $dataHolder) use ($testCase){
                $testCase->assertInstanceOf('BadaBoom\DataHolder\DataHolder', $dataHolder);
            }
        ));

        $callback = new Callback($chain);

        $callback(new \Exception());
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