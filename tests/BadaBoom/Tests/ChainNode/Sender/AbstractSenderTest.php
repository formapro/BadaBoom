<?php

namespace BadaBoom\Tests\ChainNode\Sender;

use BadaBoom\DataHolder\DataHolder;

class AbstractSenderTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     *
     * @test
     */
    public function shouldBeExtendedByAbstractChainNode()
    {
        $rc = new \ReflectionClass('BadaBoom\ChainNode\Sender\AbstractSender');
        $this->assertTrue($rc->isSubclassOf('BadaBoom\ChainNode\AbstractChainNode'));
    }

    /**
     *
     * @test
     */
    public function shouldCheckGivenFormatIntoConstructor()
    {
        $format = 'html';

        $serializer = $this->createMockSerializer();
        $serializer->expects($this->once())
            ->method('supportsSerialization')
            ->with($format)
            ->will($this->returnValue(true))
        ;

        $configuration = new DataHolder();
        $configuration->set('format', $format);

        $this->createSender($this->createMockAdapter(), $serializer, $configuration);
    }

    /**
     *
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage is not supported by serializer
     */
    public function throwExceptionWhenTryConstructWithUnsupportedSerializeFormat()
    {
        $format = 'fake';
        $serializer = $this->createMockSerializer();
        $serializer->expects($this->once())
            ->method('supportsSerialization')
            ->with($format)
            ->will($this->returnValue(false))
        ;

        $configuration = new DataHolder();
        $configuration->set('format', $format);

        $this->createSender($this->createMockAdapter(), $serializer, $configuration);
    }

    /**
     *
     * @test
     */
    public function shouldSerializeDataToSetFormatInConfiguration()
    {
        $data = new DataHolder();
        $format = 'html';

        $serializer = $this->createMockSerializer();
        $serializer->expects($this->once())
            ->method('supportsSerialization')
            ->with($format)
            ->will($this->returnValue(true))
        ;
        $serializer->expects($this->once())
            ->method('serialize')
            ->with($data, $format)
        ;
        $configuration = new DataHolder();
        $configuration->set('format', $format);

        $sender = $this->createSender($this->createMockAdapter(), $serializer, $configuration);

        $sender->serialize($data);
    }

    /**
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function createMockAdapter()
    {
        return $this->getMock('BadaBoom\Adapter\AdapterInterface');
    }

    /**
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function createMockSerializer()
    {
        return $this->getMock('Symfony\Component\Serializer\SerializerInterface');
    }

    /**
     * @param $adapter
     * @param $serializer
     * @param $configuration
     * @return AbstractSender
     */
    protected function createSender($adapter, $serializer, $configuration)
    {
        $class = $this->getSenderClass();
        return new $class($adapter, $serializer, $configuration);
    }

    /**
     *
     * @return string
     */
    protected function getSenderClass()
    {
        return $this->getMockClass('BadaBoom\ChainNode\Sender\AbstractSender', array('handle'));
    }
}