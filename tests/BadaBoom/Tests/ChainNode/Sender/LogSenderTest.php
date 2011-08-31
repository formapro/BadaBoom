<?php

namespace BadaBoom\Tests\ChainNode\Sender;

use BadaBoom\ChainNode\Sender\LogSender;
use BadaBoom\DataHolder\DataHolder;

class LogSenderTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldBeSubclassOfAbstractSender()
    {
        $rc = new \ReflectionClass('BadaBoom\ChainNode\Sender\LogSender');
        $this->assertTrue($rc->isSubclassOf('BadaBoom\ChainNode\Sender\AbstractSender'));
    }

    /**
     * @test
     */
    public function shouldImplementAppropriateConstructMethod()
    {
        $rc = new \ReflectionClass('BadaBoom\ChainNode\Sender\LogSender');
        $parameters = $rc->getMethod('__construct')->getParameters();
        $this->assertEquals(
            'BadaBoom\Adapter\Logger\LoggerAdapterInterface',
            $parameters[0]->getClass()->getName()
        );
        $this->assertEquals(
            'Symfony\Component\Serializer\SerializerInterface',
            $parameters[1]->getClass()->getName()
        );
        $this->assertEquals(
            'BadaBoom\DataHolder\DataHolderInterface',
            $parameters[2]->getClass()->getName()
        );
    }

    /**
     * @test
     */
    public function shouldLogDataWithLevelGivenByProvider()
    {
        $configuration = new DataHolder();
        $configuration->set('format', 'json');
        $configuration->set('log_level', LogSender::ALERT);

        $data = new DataHolder();
        $data->set('log_level', LogSender::CRITICAL);
        $serializedData = 'Hey! Log me.';

        $this->assertNotEquals($configuration->get('log_level'), $data->get('log_level'));

        $serializer = $this->createSerializerMock($configuration->get('format'));
        $serializer->expects($this->once())
            ->method('serialize')
            ->with($data, $configuration->get('format'))
            ->will($this->returnValue($serializedData))
        ;

        $adapter = $this->getMock('BadaBoom\Adapter\Logger\LoggerAdapterInterface');
        $adapter->expects($this->once())
            ->method('log')
            ->with($serializedData, $data->get('log_level'))
        ;

        $sender = new LogSender($adapter, $serializer, $configuration);
        $sender->handle(new \Exception(), $data);
    }

    /**
     * @test
     */
    public function shouldLogDataWithLevelGivenByConfiguration()
    {
        $configuration = new DataHolder();
        $configuration->set('log_level', LogSender::CRITICAL);
        $configuration->set('format', 'json');

        $data = new DataHolder();
        $serializedData = 'Hey! Log me.';

        $serializer = $this->createSerializerMock($configuration->get('format'));
        $serializer->expects($this->once())
            ->method('serialize')
            ->with($data, $configuration->get('format'))
            ->will($this->returnValue($serializedData))
        ;

        $adapter = $this->getMock('BadaBoom\Adapter\Logger\LoggerAdapterInterface');
        $adapter->expects($this->once())
            ->method('log')
            ->with($serializedData, $configuration->get('log_level'))
        ;

        $sender = new LogSender($adapter, $serializer, $configuration);
        $sender->handle(new \Exception(), $data);
    }

    /**
     * @test
     */
    public function shouldLogDataWithDefaultLevel()
    {
        $configuration = new DataHolder();
        $configuration->set('format', 'json');

        $data = new DataHolder();
        $serializedData = 'Hey! Log me.';

        $serializer = $this->createSerializerMock($configuration->get('format'));
        $serializer->expects($this->once())
            ->method('serialize')
            ->with($data, $configuration->get('format'))
            ->will($this->returnValue($serializedData))
        ;

        $adapter = $this->getMock('BadaBoom\Adapter\Logger\LoggerAdapterInterface');
        $adapter->expects($this->once())
            ->method('log')
            ->with($serializedData, LogSender::INFO)
        ;

        $sender = new LogSender($adapter, $serializer, $configuration);
        $sender->handle(new \Exception(), $data);
    }

    /**
     * @param string $supportedFormat
     * 
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function createSerializerMock($supportedFormat)
    {
        $serializer = $this->getMock('Symfony\Component\Serializer\SerializerInterface');
        $serializer->expects($this->any())
            ->method('supportsSerialization')
            ->with($supportedFormat)
            ->will($this->returnValue(true))
        ;

        return $serializer;
    }
}