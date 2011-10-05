<?php

namespace BadaBoom\Tests\ChainNode\Provider;

use BadaBoom\DataHolder\DataHolder;
use BadaBoom\ChainNode\Provider\ExceptionSubjectProvider;

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
    public function shouldFillDataHolderWithSubject()
    {
        $exception = new \Exception('foo');
        $expectedSubject = 'Exception: ' . $exception->getMessage();

        $data = new DataHolder();

        $provider = new ExceptionSubjectProvider();

        $provider->handle($exception, $data);

        $this->assertEquals($expectedSubject, $data->get('subject'));
    }

    /**
     * @test
     */
    public function shouldNotAddNamespaceOfExceptionToSubject()
    {
        $exception = new CustomException('foo');
        $expectedSubject = 'CustomException: ' . $exception->getMessage();

        $data = new DataHolder();

        $provider = new ExceptionSubjectProvider();

        $provider->handle($exception, $data);

        $this->assertEquals($expectedSubject, $data->get('subject'));
    }

    /**
     * @test
     */
    public function shouldDelegateHandlingToTheNextNode()
    {
        $exception = new \Exception('foo');
        $data = new DataHolder();

        $nextNode = $this->getMockForAbstractClass('BadaBoom\ChainNode\AbstractChainNode');
        $nextNode->expects($this->once())->method('handle')->with($this->equalTo($exception), $this->equalTo($data));

        $provider = new ExceptionSubjectProvider();
        $provider->nextNode($nextNode);

        $provider->handle($exception, $data);
    }
}

class CustomException extends \Exception {}