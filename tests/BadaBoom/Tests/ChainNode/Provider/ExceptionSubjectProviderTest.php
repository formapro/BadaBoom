<?php

namespace BadaBoom\Tests\ChainNode\Provider;

use BadaBoom\DataHolder\DataHolder;
use BadaBoom\ChainNode\Provider\ExceptionSubjectProvider;

class ExceptionSubjectProviderTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldBeExtendedByAbstractProvider()
    {
        $rc = new \ReflectionClass('BadaBoom\ChainNode\Provider\ExceptionSubjectProvider');
        $this->assertTrue($rc->isSubclassOf('BadaBoom\ChainNode\Provider\AbstractProvider'));
    }

    /**
     * @test
     */
    public function shouldNotFillSubjectIfNoExceptionGiven()
    {
        $data = new DataHolder();

        $provider = new ExceptionSubjectProvider();
        $provider->handle($data);

        $this->assertFalse($data->has('subject'));
    }

    /**
     * @test
     */
    public function shouldDelegateHandlingToTheNextNode()
    {
        $data = new DataHolder();

        $nextNode = $this->getMockForAbstractClass('BadaBoom\ChainNode\AbstractChainNode');
        $nextNode->expects($this->once())->method('handle')->with($this->equalTo($data));

        $provider = new ExceptionSubjectProvider();
        $provider->nextNode($nextNode);

        $provider->handle($data);
    }

    /**
     * @test
     */
    public function shouldFillDataHolderWithSubject()
    {
        $e = new \Exception('foo', 123);
        $expectedSubject = get_class($e) . ': ' . $e->getMessage();

        $data = new DataHolder();
        $data->set('exception', $e);

        $provider = new ExceptionSubjectProvider();
        $provider->handle($data);

        $this->assertEquals($expectedSubject, $data->get('subject'));
    }
}
