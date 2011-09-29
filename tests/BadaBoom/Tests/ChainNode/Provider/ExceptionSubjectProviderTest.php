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
        $e = new \Exception('foo');
        $expectedSubject = 'Exception: ' . $e->getMessage();

        $data = new DataHolder();

        $provider = new ExceptionSubjectProvider();

        $provider->handle($e, $data);

        $this->assertEquals($expectedSubject, $data->get('subject'));
    }

    /**
     * @test
     */
    public function shouldNotAddNamespaceOfExceptionToSubject()
    {
        $e = new CustomException('foo');
        $expectedSubject = 'CustomException: ' . $e->getMessage();

        $data = new DataHolder();

        $provider = new ExceptionSubjectProvider();

        $provider->handle($e, $data);

        $this->assertEquals($expectedSubject, $data->get('subject'));
    }

    /**
     * @test
     */
    public function shouldDelegateHandlingToTheNextNode()
    {
        $e = new \Exception('foo');
        $data = new DataHolder();

        $nextNode = $this->getMockForAbstractClass('BadaBoom\ChainNode\AbstractChainNode');
        $nextNode->expects($this->once())->method('handle')->with($this->equalTo($e), $this->equalTo($data));

        $provider = new ExceptionSubjectProvider();
        $provider->nextNode($nextNode);

        $provider->handle($e, $data);
    }
}

class CustomException extends \Exception {}