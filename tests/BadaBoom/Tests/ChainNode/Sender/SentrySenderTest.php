<?php

namespace BadaBoom\Tests\ChainNode\Sender;

use BadaBoom\ChainNode\Sender\SentrySender;
use BadaBoom\Context;

class SentrySenderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldBeSubclassOfAbstractSender()
    {
        $rc = new \ReflectionClass('BadaBoom\ChainNode\Sender\SentrySender');
        $this->assertTrue($rc->isSubclassOf('BadaBoom\ChainNode\AbstractChainNode'));
    }

    /**
     * @test
     */
    public function couldBeConstructedWithRavenClientArgument()
    {
        new SentrySender($this->getRavenClientMock());
    }

    /**
     * @test
     */
    public function shouldRunCaptureExceptionMethodOnce()
    {
        $context = new Context(new \Exception());
        $exception = $context->getException();

        $ravenClientMock = $this->getRavenClientMock();
        $ravenClientMock->expects($this->once())
            ->method('captureException')
            ->with($this->identicalTo($exception))
        ;

        $senderMock = $this->getMock(
            '\BadaBoom\ChainNode\Sender\SentrySender',
            array('handleNextNode'),
            array($ravenClientMock)
        );

        $senderMock->expects($this->once())
            ->method('handleNextNode')
            ->with($this->identicalTo($context))
        ;

        $senderMock->handle($context);
    }

    protected function getRavenClientMock()
    {
        return $this->getMock('Raven_Client');
    }
}