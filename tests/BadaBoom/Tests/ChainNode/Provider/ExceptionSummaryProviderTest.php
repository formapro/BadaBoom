<?php
namespace BadaBoom\Tests\ChainNode\Provider;

use BadaBoom\ChainNode\Provider\ExceptionSummaryProvider;
use BadaBoom\Context;

class ExceptionSummaryProviderTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        unset($_SERVER['HTTP_HOST'], $_SERVER['REQUEST_URI'], $_SERVER['argv']);
    }

    /**
     *
     * @test
     */
    public function shouldBeSubclassOfAbstractProvider()
    {
        $rc = new \ReflectionClass('BadaBoom\ChainNode\Provider\ExceptionSummaryProvider');
        $this->assertTrue($rc->isSubclassOf('BadaBoom\ChainNode\Provider\AbstractProvider'));
    }

    /**
     *
     * @test
     */
    public function shouldSetDefaultSectionNameIfNotProvided()
    {
        $context = new Context(new \Exception);

        $provider = new ExceptionSummaryProvider();
        $provider->handle($context);

        $this->assertTrue($context->hasVar('summary'));
    }

    /**
     *
     * @test
     */
    public function shouldUseCustomSectionNameIfProvided()
    {
        $context = new Context(new \Exception);

        $provider = new ExceptionSummaryProvider('barSection');
        $provider->handle($context);

        $this->assertTrue($context->hasVar('barSection'));
        $this->assertFalse($context->hasVar('summary'));
    }

    /**
     *
     * @test
     */
    public function shouldFillDataHolderWithExceptionInfo()
    {
        $context = new Context(new \Exception('foo', 123));

        $provider = new ExceptionSummaryProvider();
        $provider->handle($context);

        $summary = $context->getVar('summary');
        $this->assertInternalType('array', $summary);

        $this->assertArrayHasKey('class', $summary);
        $this->assertArrayHasKey('uri', $summary);
        $this->assertArrayHasKey('code', $summary);
        $this->assertArrayHasKey('message', $summary);
        $this->assertArrayHasKey('file', $summary);
    }

    /**
     * @test
     */
    public function shouldSetUndefinedUriIfCannotGuess()
    {
        //guard
        $this->assertArrayNotHasKey('HTTP_HOST', $_SERVER);
        $this->assertArrayNotHasKey('REQUEST_URI', $_SERVER);
        $this->assertArrayNotHasKey('argv', $_SERVER);

        $context = new Context(new \Exception('foo', 123));

        $provider = new ExceptionSummaryProvider();
        $provider->handle($context);

        $summary = $context->getVar('summary');

        $this->assertEquals('undefined', $summary['uri']);
    }

    /**
     * @test
     */
    public function shouldSetRequestedUrlIfDefined()
    {
        $_SERVER['HTTP_HOST'] = 'badaboom.com';
        $_SERVER['REQUEST_URI'] = '/exception.html?foo=foo&bar=bar';

        $context = new Context(new \Exception('foo', 123));

        $provider = new ExceptionSummaryProvider();
        $provider->handle($context);

        $summary = $context->getVar('summary');

        $this->assertEquals('http://badaboom.com/exception.html?foo=foo&bar=bar', $summary['uri']);
    }

    /**
     * @test
     */
    public function shouldSetCommandLineIfDefined()
    {
        $_SERVER['argv'] = array(
            'php',
            'command',
            'bar',
            '--foo=foo',
        );

        $context = new Context(new \Exception('foo', 123));

        $provider = new ExceptionSummaryProvider();
        $provider->handle($context);

        $summary = $context->getVar('summary');

        $this->assertEquals('php command bar --foo=foo', $summary['uri']);
    }

    /**
     * @test
     */
    public function shouldSetCodeForNotErrorExceptions()
    {
        $expectedCode = 321;

        $context = new Context(new \Exception('foo', $expectedCode));

        $provider = new ExceptionSummaryProvider();
        $provider->handle($context);

        $summary = $context->getVar('summary');

        $this->assertEquals($expectedCode, $summary['code']);
    }

    /**
     * @test
     */
    public function shouldSetHumanReadableSeverityForErrorException()
    {
        $exception = new \ErrorException('foo', $code = 123, E_COMPILE_ERROR, $file = 'foo', $line = 123);
        $context = new Context($exception);

        $provider = new ExceptionSummaryProvider();
        $provider->handle($context);

        $summary = $context->getVar('summary');

        $this->assertEquals('E_COMPILE_ERROR', $summary['code']);
    }

    /**
     * @test
     */
    public function shouldSetUnknownHumanReadableSeverityForNotStandardErrorException()
    {
        $notStandardSeverity = 556234;

        $exception = new \ErrorException('foo', $code = 123, $notStandardSeverity, $file = 'foo', $line = 123);
        $context = new Context($exception);

        $provider = new ExceptionSummaryProvider();
        $provider->handle($context);

        $summary = $context->getVar('summary');

        $this->assertEquals('E_UNKNOWN', $summary['code']);
    }

    /**
     * @test
     */
    public function shouldDelegateHandlingToNextChainNode()
    {
        $context = new Context(new \Exception);

        $nextChainNode = $this->createMockChainNode();
        $nextChainNode
            ->expects($this->once())
            ->method('handle')
            ->with($context)
        ;

        $provider = new ExceptionSummaryProvider();
        $provider->nextNode($nextChainNode);

        $provider->handle($context);
    }

    protected function createMockChainNode()
    {
        return $this->getMockForAbstractClass('BadaBoom\ChainNode\AbstractChainNode');
    }
}