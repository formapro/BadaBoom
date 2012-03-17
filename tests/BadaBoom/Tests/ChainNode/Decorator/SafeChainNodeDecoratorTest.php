<?php
namespace BadaBoom\Tests\ChainNode\Decorator;

use BadaBoom\ChainNode\Decorator\SafeChainNodeDecorator;
use BadaBoom\DataHolder\DataHolder;

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
        new SafeChainNodeDecorator($this->createChainNode());
    }

    /**
     * @test
     */
    public function shouldProxyHandleToChainNodeSetInConstructor()
    {
        $expectedException = new \Exception();
        $expectedDataHolderMock = $this->createDataHolderMock();

        $chainNodeMock = $this->createChainNode();
        $chainNodeMock
            ->expects($this->once())
            ->method('handle')
            ->with(
                $this->equalTo($expectedException),
                $this->equalTo($expectedDataHolderMock)
            )
        ;

        $safe = new SafeChainNodeDecorator($chainNodeMock);

        $safe->handle($expectedException, $expectedDataHolderMock);
    }

    /**
     * @test
     */
    public function shouldProxyNextNodeToChainNodeSetInConstructor()
    {
        $expectedNextChainNode = $this->createChainNode();

        $chainNodeMock = $this->createChainNode();
        $chainNodeMock
                ->expects($this->once())
                ->method('nextNode')
                ->with(
            $this->equalTo($expectedNextChainNode)
        )
        ;

        $safe = new SafeChainNodeDecorator($chainNodeMock);

        $safe->nextNode($expectedNextChainNode);
    }

    /**
     * @test
     */
    public function shouldReturnResultOfInternalChainNodeWhileNextNodeCall()
    {
        $expectedNextNodeChainNode = $this->createChainNode();

        $chainNodeMock = $this->createChainNode();
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
    public function shouldSetChainClassToDataHolderIfThrownWhileHandlingInternalChainNode()
    {
        $internalException = new \Exception('internal exception message', 123);

        $chainNodeMock = $this->createChainNode();
        $chainNodeMock
            ->expects($this->once())
            ->method('handle')
            ->will($this->throwException($internalException))
        ;

        $safe = new SafeChainNodeDecorator($chainNodeMock);

        $safe->handle(new \Exception(), $dataHolder = new DataHolder());

        $chainExceptions = $dataHolder->get('chain_exceptions');
        $this->assertCount(1, $chainExceptions);

        $chainException = array_shift($chainExceptions);
        $this->assertArrayHasKey('chain', $chainException);
        $this->assertEquals(get_class($chainNodeMock), $chainException['chain']);
    }

    /**
     * @test
     */
    public function shouldSetExceptionInfoToDataHolderIfThrownWhileHandlingInternalChainNode()
    {
        $internalException = new \Exception('internal exception message', 123);

        $chainNodeMock = $this->createChainNode();
        $chainNodeMock
            ->expects($this->once())
            ->method('handle')
            ->will($this->throwException($internalException))
        ;

        $safe = new SafeChainNodeDecorator($chainNodeMock);

        $safe->handle(new \Exception(), $dataHolder = new DataHolder());

        $chainExceptions = $dataHolder->get('chain_exceptions');
        $this->assertCount(1, $chainExceptions);

        $chainException = array_shift($chainExceptions);

        $this->assertArrayHasKey('class', $chainException);
        $this->assertEquals(get_class($internalException), $chainException['class']);

        $this->assertArrayHasKey('message', $chainException);
        $this->assertEquals($internalException->getMessage(), $chainException['message']);

        $this->assertArrayHasKey('code', $chainException);
        $this->assertEquals($internalException->getCode(), $chainException['code']);

        $this->assertArrayHasKey('line', $chainException);
        $this->assertEquals($internalException->getLine(), $chainException['line']);

        $this->assertArrayHasKey('file', $chainException);
        $this->assertEquals($internalException->getFile(), $chainException['file']);
    }

    /**
     * @test
     */
    public function shouldNotSetExceptionPreviousInfoToDataHolderIfThrownWhileHandlingInternalChainNode()
    {
        $internalException = new \Exception('internal exception message', 123);

        $chainNodeMock = $this->createChainNode();
        $chainNodeMock
            ->expects($this->once())
            ->method('handle')
            ->will($this->throwException($internalException))
        ;

        $safe = new SafeChainNodeDecorator($chainNodeMock);

        $safe->handle(new \Exception(), $dataHolder = new DataHolder());

        $chainExceptions = $dataHolder->get('chain_exceptions');
        $this->assertCount(1, $chainExceptions);

        $chainException = array_shift($chainExceptions);

        $this->assertArrayHasKey('has_previous', $chainException);
        $this->assertFalse($chainException['has_previous']);
    }

    /**
     * @test
     */
    public function shouldSetExceptionPreviousInfoToDataHolderIfThrownWhileHandlingInternalChainNode()
    {
        $internalException = new \Exception('internal exception message', 123, new \Exception());

        $chainNodeMock = $this->createChainNode();
        $chainNodeMock
                ->expects($this->once())
                ->method('handle')
                ->will($this->throwException($internalException))
        ;

        $safe = new SafeChainNodeDecorator($chainNodeMock);

        $safe->handle(new \Exception(), $dataHolder = new DataHolder());

        $chainExceptions = $dataHolder->get('chain_exceptions');
        $this->assertCount(1, $chainExceptions);

        $chainException = array_shift($chainExceptions);

        $this->assertArrayHasKey('has_previous', $chainException);
        $this->assertTrue($chainException['has_previous']);
    }

    /**
     * @test
     */
    public function shouldNotReplacePreviouslyAddedChainExceptions()
    {
        $internalException = new \Exception();
        $dataHolder = new DataHolder();

        $chainNodeMock = $this->createChainNode();
        $chainNodeMock
                ->expects($this->exactly(2))
                ->method('handle')
                ->will($this->throwException($internalException))
        ;

        $safe = new SafeChainNodeDecorator($chainNodeMock);

        $safe->handle(new \Exception(), $dataHolder);

        $this->assertCount(1, $dataHolder->get('chain_exceptions'));

        $safe->handle(new \Exception(), $dataHolder);

        $this->assertCount(2, $dataHolder->get('chain_exceptions'));
    }

    protected function createChainNode()
    {
        return $this->getMock('BadaBoom\ChainNode\ChainNodeInterface');
    }

    protected function createDataHolderMock()
    {
        return $this->getMock('BadaBoom\DataHolder\DataHolderInterface');
    }
}
