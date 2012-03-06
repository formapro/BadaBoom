<?php

namespace BadaBoom\Tests\ChainNode\Provider;

use BadaBoom\DataHolder\DataHolder;
use BadaBoom\ChainNode\Provider\ExceptionSummaryProvider;

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
        $exception = new \Exception('foo');
        $data = new DataHolder;

        $provider = new ExceptionSummaryProvider();
        $provider->handle($exception, $data);

        $this->assertTrue($data->has('summary'));
    }

    /**
     *
     * @test
     */
    public function shouldUseCustomSectionNameIfProvided()
    {
        $exception = new \Exception('foo');
        $data = new DataHolder();

        $provider = new ExceptionSummaryProvider('barSection');
        $provider->handle($exception, $data);

        $this->assertTrue($data->has('barSection'));
        $this->assertFalse($data->has('summary'));
    }

    /**
     *
     * @test
     */
    public function shouldFillDataHolderWithExceptionInfo()
    {
        $exception = new \Exception('foo', 123);
        $data = new DataHolder();

        $provider = new ExceptionSummaryProvider();
        $provider->handle($exception, $data);

        $summary = $data->get('summary');
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

        $exception = new \Exception('foo', 123);
        $data = new DataHolder();

        $provider = new ExceptionSummaryProvider();
        $provider->handle($exception, $data);

        $summary = $data->get('summary');

        $this->assertEquals('undefined', $summary['uri']);
    }

    /**
     * @test
     */
    public function shouldSetRequestedUrlIfDefined()
    {
        $_SERVER['HTTP_HOST'] = 'badaboom.com';
        $_SERVER['REQUEST_URI'] = '/exception.html?foo=foo&bar=bar';

        $exception = new \Exception('foo', 123);
        $data = new DataHolder();

        $provider = new ExceptionSummaryProvider();
        $provider->handle($exception, $data);

        $summary = $data->get('summary');

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

        $exception = new \Exception('foo', 123);
        $data = new DataHolder();

        $provider = new ExceptionSummaryProvider();
        $provider->handle($exception, $data);

        $summary = $data->get('summary');

        $this->assertEquals('php command bar --foo=foo', $summary['uri']);
    }

    /**
     * @test
     */
    public function shouldSetCodeForNotErrorExceptions()
    {
        $expectedCode = 123;

        $exception = new \Exception('foo', $expectedCode);
        $data = new DataHolder();

        $provider = new ExceptionSummaryProvider();
        $provider->handle($exception, $data);

        $summary = $data->get('summary');

        $this->assertEquals($expectedCode, $summary['code']);
    }

    /**
     * @test
     */
    public function shouldSetHumanReadableSeverityForErrorException()
    {
        $exception = new \ErrorException('foo', $code = 123, E_COMPILE_ERROR, $file = 'foo', $line = 123);
        $data = new DataHolder();

        $provider = new ExceptionSummaryProvider();
        $provider->handle($exception, $data);

        $summary = $data->get('summary');

        $this->assertEquals('E_COMPILE_ERROR', $summary['code']);
    }

    /**
     * @test
     */
    public function shouldSetUnknownHumanReadableSeverityForNotStandardErrorException()
    {
        $notStandardSeverity = 556234;

        $exception = new \ErrorException('foo', $code = 123, $notStandardSeverity, $file = 'foo', $line = 123);
        $data = new DataHolder();

        $provider = new ExceptionSummaryProvider();
        $provider->handle($exception, $data);

        $summary = $data->get('summary');

        $this->assertEquals('E_UNKNOWN', $summary['code']);
    }


    /**
     *
     * @test
     */
    public function shouldDelegateHandlingToNextChainNode()
    {
        $exception = new \Exception;
        $data = new DataHolder;

        $nextChainNode = $this->createMockChainNode();
        $nextChainNode->expects($this->once())->method('handle')->with($this->equalTo($exception), $this->equalTo($data));

        $provider = new ExceptionSummaryProvider();
        $provider->nextNode($nextChainNode);

        $provider->handle($exception, $data);
    }

    protected function createMockChainNode()
    {
        return $this->getMockForAbstractClass('BadaBoom\ChainNode\AbstractChainNode');
    }
}