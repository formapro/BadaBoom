<?php

namespace BadaBoom\Tests\ChainNode\Sender;

use BadaBoom\ChainNode\Sender\SentrySender;
use BadaBoom\Context;

class SentrySenderTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (false == class_exists('Raven_Client')) {
            $this->markTestSkipped('The Raven Client is not available.');
        }
    }

    /**
     * @test
     */
    public function shouldBeSubclassOfAbstractChainNode()
    {
        $rc = new \ReflectionClass('BadaBoom\ChainNode\Sender\SentrySender');
        $this->assertTrue($rc->isSubclassOf('BadaBoom\ChainNode\AbstractChainNode'));
    }

    /**
     * @test
     */
    public function couldBeConstructedWithRavenClientArgument()
    {
        new SentrySender($this->createRavenClientMock());
    }

    /**
     * @test
     */
    public function shouldCaptureExceptionAndDelegateHandlingToNextChainNode()
    {
        $context = new Context(new \Exception());
        $exception = $context->getException();

        $ravenClientMock = $this->createRavenClientMock();
        $ravenClientMock->expects($this->once())
            ->method('captureException')
            ->with($this->identicalTo($exception))
        ;

        $nextChainNode = $this->createMockChainNode();
        $nextChainNode
            ->expects($this->once())
            ->method('handle')
            ->with($context)
        ;

        $sender = new SentrySender($ravenClientMock);
        $sender->nextNode($nextChainNode);
        $sender->handle($context);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function createRavenClientMock()
    {
        return $this->getMock('Raven_Client');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function createMockChainNode()
    {
        return $this->getMockForAbstractClass('BadaBoom\ChainNode\AbstractChainNode');
    }
}