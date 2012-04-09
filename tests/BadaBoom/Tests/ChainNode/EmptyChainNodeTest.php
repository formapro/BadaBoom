<?php

namespace BadaBoom\Tests\ChainNode;

use BadaBoom\DataHolder\DataHolder;
use BadaBoom\ChainNode\EmptyChainNode;

class EmptyChainNodeTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     * @test
     */
    public function shouldBeSubclassOfAbstractChainNodeInterface()
    {
        $rc = new \ReflectionClass('BadaBoom\ChainNode\EmptyChainNode');
        $this->assertTrue($rc->isSubclassOf('BadaBoom\ChainNode\AbstractChainNode'));
    }

    /**
     * @test
     */
    public function couldBeConstructedWithoutAnyArguments()
    {
        new EmptyChainNode;
    }

    /**
     *
     * @test
     */
    public function shouldDelegateHandlingToNextChainNode()
    {
        $exception = new \Exception;
        $data = new DataHolder;

        $emptyChainNode = new EmptyChainNode;
        $nextChainNode = $this->createMockChainNode();
        $nextChainNode
            ->expects($this->once())
            ->method('handle')
            ->with(
                $this->equalTo($exception),
                $this->equalTo($data)
            )
        ;

        $emptyChainNode->nextNode($nextChainNode);

        $emptyChainNode->handle($exception, $data);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function createMockChainNode()
    {
        return $this->getMockForAbstractClass('BadaBoom\ChainNode\AbstractChainNode');
    }
}