<?php
namespace BadaBoom\Tests\ChainNode\Decorator;

use BadaBoom\ChainNode\Decorator\SafeChainNodeDecorator;
use BadaBoom\Context;

class SafeChainNodeDecoratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldImplementChainNodeInterface()
    {
        $rc = new \ReflectionClass('BadaBoom\ChainNode\Decorator\SafeChainNodeDecorator');
        $this->assertTrue($rc->implementsInterface('BadaBoom\ChainNode\ChainNodeInterface'));
    }

    /**
     * @test
     */
    public function couldBeConstructedWithChainNodeAsArgument()
    {
        new SafeChainNodeDecorator($this->createChainNodeMock());
    }

    /**
     * @test
     */
    public function shouldProxyHandleToChainNodeSetInConstructor()
    {
        $expectedContext = new Context(new \Exception());
        
        $chainNodeMock = $this->createChainNodeMock();
        $chainNodeMock
            ->expects($this->once())
            ->method('handle')
            ->with($expectedContext)
        ;

        $safe = new SafeChainNodeDecorator($chainNodeMock);

        $safe->handle($expectedContext);
    }

    /**
     * @test
     */
    public function shouldProxyNextNodeToChainNodeSetInConstructor()
    {
        $expectedNextChainNode = $this->createChainNodeMock();

        $chainNodeMock = $this->createChainNodeMock();
        $chainNodeMock
            ->expects($this->once())
            ->method('nextNode')
            ->with($expectedNextChainNode)
        ;

        $safe = new SafeChainNodeDecorator($chainNodeMock);

        $safe->nextNode($expectedNextChainNode);
    }

    /**
     * @test
     */
    public function shouldReturnResultOfInternalChainNodeWhileNextNodeCall()
    {
        $expectedNextNodeChainNode = $this->createChainNodeMock();

        $chainNodeMock = $this->createChainNodeMock();
        $chainNodeMock
            ->expects($this->any())
            ->method('nextNode')
            ->will($this->returnValue($expectedNextNodeChainNode))
        ;

        $safe = new SafeChainNodeDecorator($chainNodeMock);

        $this->assertSame($expectedNextNodeChainNode, $safe->nextNode($expectedNextNodeChainNode));
    }

    /**
     * @test
     */
    public function shouldSetChainClassToContextIfThrownWhileHandlingInternalChainNode()
    {
        $internalException = new \Exception('internal exception message', 123);
        $context = new Context(new \Exception());

        $chainNodeMock = $this->createChainNodeMock();
        $chainNodeMock
            ->expects($this->once())
            ->method('handle')
            ->will($this->throwException($internalException))
        ;

        $safe = new SafeChainNodeDecorator($chainNodeMock);

        $safe->handle($context);

        $this->assertTrue($context->hasVar('chain_exceptions'));
        $this->assertInternalType('array', $context->getVar('chain_exceptions'));
        
        $chainExceptions = $context->getVar('chain_exceptions');
        $this->assertCount(1,  $chainExceptions);
        $this->assertArrayHasKey('chain', $chainExceptions[0]);
        $this->assertEquals(get_class($chainNodeMock), $chainExceptions[0]['chain']);
    }

    /**
     * @test
     */
    public function shouldSetExceptionInfoToContextIfThrownWhileHandlingInternalChainNode()
    {
        $internalException = new \InvalidArgumentException('internal exception message', 123);
        $context = new Context(new \Exception());

        $chainNodeMock = $this->createChainNodeMock();
        $chainNodeMock
            ->expects($this->once())
            ->method('handle')
            ->will($this->throwException($internalException))
        ;

        $safe = new SafeChainNodeDecorator($chainNodeMock);

        $safe->handle($context);

        $this->assertTrue($context->hasVar('chain_exceptions'));
        $this->assertInternalType('array', $context->getVar('chain_exceptions'));

        $chainExceptions = $context->getVar('chain_exceptions');
        $this->assertCount(1,  $chainExceptions);
        $this->assertArrayHasKey('exception', $chainExceptions[0]);
        $this->assertEquals((string) $internalException, $chainExceptions[0]['exception']);
    }

    /**
     * @test
     */
    public function shouldNotReplacePreviouslyAddedChainExceptions()
    {
        $internalException = new \Exception();
        $context = new Context(new \Exception());

        $chainNodeMock = $this->createChainNodeMock();
        $chainNodeMock
                ->expects($this->exactly(2))
                ->method('handle')
                ->will($this->throwException($internalException))
        ;

        $safe = new SafeChainNodeDecorator($chainNodeMock);

        $safe->handle($context);

        $this->assertCount(1, $context->getVar('chain_exceptions'));

        $safe->handle($context);

        $this->assertCount(2, $context->getVar('chain_exceptions'));
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\BadaBoom\ChainNode\ChainNodeInterface
     */
    protected function createChainNodeMock()
    {
        return $this->getMock('BadaBoom\ChainNode\ChainNodeInterface');
    }
}