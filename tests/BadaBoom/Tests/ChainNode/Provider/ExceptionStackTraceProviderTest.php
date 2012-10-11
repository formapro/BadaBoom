<?php
namespace BadaBoom\Tests\ChainNode\Provider;

use BadaBoom\ChainNode\Provider\ExceptionStackTraceProvider;
use BadaBoom\Context;

class ExceptionStackTraceProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldBeSubclassOfAbstractProvider()
    {
        $rc = new \ReflectionClass('BadaBoom\ChainNode\Provider\ExceptionStackTraceProvider');
        $this->assertTrue($rc->isSubclassOf('BadaBoom\ChainNode\Provider\AbstractProvider'));
    }

    /**
     * @test
     */
    public function shouldSetDefaultSectionNameIfNotProvided()
    {
        $context = new Context(new \Exception('foo'));

        $provider = new ExceptionStackTraceProvider();
        $provider->handle($context);

        $this->assertTrue($context->hasVar('stacktrace'));
    }

    /**
     *
     * @test
     */
    public function shouldUseCustomSectionNameIfProvided()
    {
        $context = new Context(new \Exception('foo'));

        $provider = new ExceptionStackTraceProvider('barSection');
        $provider->handle($context);

        $this->assertTrue($context->hasVar('barSection'));
        $this->assertFalse($context->hasVar('stacktrace'));
    }

    /**
     *
     * @test
     */
    public function shouldFillDataHolderWithExceptionStackTrace()
    {
        $context = new Context(new \Exception('foo'));

        $provider = new ExceptionStackTraceProvider();
        $provider->handle($context);

        $this->assertInternalType('string', $context->getVar('stacktrace'));
        $this->assertEquals(
            (string) $context->getException(), 
            $context->getVar('stacktrace')
        );
    }

    /**
     *
     * @test
     */
    public function shouldDelegateHandlingToNextChainNode()
    {
        $context = new Context(new \Exception('foo'));

        $nextChainNode = $this->createMockChainNode();
        $nextChainNode
            ->expects($this->once())
            ->method('handle')
            ->with($context)
        ;

        $provider = new ExceptionStackTraceProvider();
        $provider->nextNode($nextChainNode);

        $provider->handle($context);
    }

    protected function createMockChainNode()
    {
        return $this->getMockForAbstractClass('BadaBoom\ChainNode\AbstractChainNode');
    }
}