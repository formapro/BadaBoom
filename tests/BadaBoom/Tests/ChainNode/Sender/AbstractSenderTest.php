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
     * 
     * @test
     */
    public function shouldTakeAdapterAndSerializerInConstructor()
    {
        $class = $this->getMockedAbstractSenderClass();
        new $class($this->createMockAdapter(), $this->createMockSerializer());
    }

    /**
     * 
     * @test
     */
    public function shouldAllowToSetFormatSupportedBySerializer()
    {
        $format = 'html';
        $serializer = $this->createMockSerializer();
        $serializer->expects($this->once())
            ->method('supportsSerialization')
            ->with($format)
            ->will($this->returnValue(true))
        ;

        $class = $this->getMockedAbstractSenderClass();
        $sender = new $class($this->createMockAdapter(), $serializer);
        $sender->setFormat($format);
    }

    /**
     *
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage is not supported by serializer
     */
    public function throwExceptionWhenTrySetUnsupportedFormat()
    {
        $format = 'fake';
        $serializer = $this->createMockSerializer();
        $serializer->expects($this->once())
            ->method('supportsSerialization')
            ->with($format)
            ->will($this->returnValue(false))
        ;

        $class = $this->getMockedAbstractSenderClass();
        $sender = new $class($this->createMockAdapter(), $serializer);
        $sender->setFormat('fake');
    }

    /**
     * 
     * @test
     */
    public function shouldSerializeDataToSetFormat()
    {
        $format = 'html';
        $data = new DataHolder();

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

        $class = $this->getMockedAbstractSenderClass();
        $sender = new $class($this->createMockAdapter(), $serializer);
        $sender->setFormat($format);
        
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
     *
     * @param $adapter
     * @param $serializer
     * @return AdapterSender
     */
    protected function createSender($adapter, $serializer)
    {
        $class = $this->getMockedAbstractSenderClass();
        return new $class($adapter, $serializer);
    }

    /**
     *
     * @return string
     */
    protected function getMockedAbstractSenderClass()
    {
        return $this->getMockClass('BadaBoom\ChainNode\Sender\AbstractSender', array('handle'));
    }
}