<?php

namespace BadaBoom\Tests\ChainNode\Provider;

use BadaBoom\DataHolder\DataHolder;
use BadaBoom\ChainNode\Provider\ExceptionStackTraceProvider;

class ExceptionStackTraceProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     * @test
     */
    public function shouldBeSubclassOfAbstractProvider()
    {
        $rc = new \ReflectionClass('BadaBoom\ChainNode\Provider\ExceptionStackTraceProvider');
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

        $provider = new ExceptionStackTraceProvider();
        $provider->handle($exception, $data);

        $this->assertTrue($data->has('stacktrace'));
    }

    /**
     *
     * @test
     */
    public function shouldUseCustomSectionNameIfProvided()
    {
        $exception = new \Exception('foo');
        $data = new DataHolder();

        $provider = new ExceptionStackTraceProvider('barSection');
        $provider->handle($exception, $data);

        $this->assertTrue($data->has('barSection'));
        $this->assertFalse($data->has('summary'));
    }

    /**
     *
     * @test
     */
    public function shouldFillDataHolderWithExceptionStackTrace()
    {
        $exception = new \Exception('foo');
        $data = new DataHolder();

        $provider = new ExceptionStackTraceProvider();
        $provider->handle($exception, $data);

        $stacktrace = $data->get('stacktrace');
        $this->assertInternalType('string', $stacktrace);
        $this->assertEquals((string) $exception, $stacktrace);
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

        $provider = new ExceptionStackTraceProvider();
        $provider->nextNode($nextChainNode);

        $provider->handle($exception, $data);
    }

    protected function createMockChainNode()
    {
        return $this->getMockForAbstractClass('BadaBoom\ChainNode\AbstractChainNode');
    }
}