<?php
namespace BadaBoom\Tests\ChainNode\Provider;

use BadaBoom\ChainNode\Provider\ExceptionSubjectProvider;
use BadaBoom\Context;

class ExceptionSubjectProviderTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldBeSubClassOfAbstractProvider()
    {
        $rc = new \ReflectionClass('BadaBoom\ChainNode\Provider\ExceptionSubjectProvider');
        $this->assertTrue($rc->isSubclassOf('BadaBoom\ChainNode\Provider\AbstractProvider'));
    }

    /**
     * @test
     */
    public function shouldSetDefaultSectionNameIfNotProvided()
    {
        $context = new Context(new \Exception('foo'));

        $provider = new ExceptionSubjectProvider();
        $provider->handle($context);

        $this->assertTrue($context->hasVar('subject'));
    }

    /**
     *
     * @test
     */
    public function shouldUseCustomSectionNameIfProvided()
    {
        $context = new Context(new \Exception('foo'));

        $provider = new ExceptionSubjectProvider('barSection');
        $provider->handle($context);

        $this->assertTrue($context->hasVar('barSection'));
        $this->assertFalse($context->hasVar('subject'));
    }

    /**
     * @test
     */
    public function shouldFillContextWithSubject()
    {
        $exception = new \Exception('foo');
        $context = new Context($exception);
        
        $expectedSubject = 'Exception: ' . $exception->getMessage();

        $provider = new ExceptionSubjectProvider();

        $provider->handle($context);

        $this->assertEquals($expectedSubject, $context->getVar('subject'));
    }

    /**
     * @test
     */
    public function shouldNotAddNamespaceOfExceptionToSubject()
    {
        $exception = new CustomException('foo');
        $context = new Context($exception);
        $expectedSubject = 'CustomException: ' . $exception->getMessage();

        $provider = new ExceptionSubjectProvider();

        $provider->handle($context);

        $this->assertEquals($expectedSubject, $context->getVar('subject'));
    }

    /**
     * @test
     */
    public function shouldDelegateHandlingToTheNextNode()
    {
        $context = new Context(new \Exception('foo'));

        $nextNode = $this->getMockForAbstractClass('BadaBoom\ChainNode\AbstractChainNode');
        $nextNode
            ->expects($this->once())
            ->method('handle')
            ->with($context)
        ;

        $provider = new ExceptionSubjectProvider();
        $provider->nextNode($nextNode);

        $provider->handle($context);
    }
}

class CustomException extends \Exception {}