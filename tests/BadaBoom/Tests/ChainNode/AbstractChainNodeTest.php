<?php

namespace BadaBoom\Tests\ChainNode;

use BadaBoom\DataHolder\DataHolder;

class AbstractChainNodeTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     * @test
     */
    public function shouldImplementChainNodeInterface()
    {
        $rc = new \ReflectionClass('BadaBoom\\ChainNode\\AbstractChainNode');
        $this->assertTrue($rc->implementsInterface('BadaBoom\\ChainNode\\ChainNodeInterface'));
    }

    /**
     *
     * @test
     */
    public function shouldAllowToSetNextNode()
    {
        $chainNode = $this->createMockChainNode();
        $nextChainNode = $this->createMockChainNode();

        $chainNode->nextNode($nextChainNode);
    }

    /**
     *
     * @test
     */
    public function shouldReturnNextNodeAfterChaining()
    {
        $chainNode = $this->createMockChainNode();
        $nextChainNode = $this->createMockChainNode();

        $this->assertSame($nextChainNode, $chainNode->nextNode($nextChainNode));
    }

    /**
     *
     * @test
     */
    public function shouldDelegateHandlingToNextChainNode()
    {
        $data = new DataHolder;

        $chainNode = $this->createMockChainNode();
        $nextChainNode = $this->createMockChainNode();
        $nextChainNode->expects($this->once())->method('handle')->with($this->equalTo($data));

        $chainNode->nextNode($nextChainNode);

        $chainNode->handleNextNode($data);
    }

    /**
     *
     * @test
     */
    public function shouldNotDelegateHandlingIfNextChainNodeIsUndefined()
    {
        $this->createMockChainNode()->handleNextNode(new DataHolder);
    }

    protected function createMockChainNode()
    {
        return $this->getMockForAbstractClass('BadaBoom\\ChainNode\\AbstractChainNode');
    }
}
