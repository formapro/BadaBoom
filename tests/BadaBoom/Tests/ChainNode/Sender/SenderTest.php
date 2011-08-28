<?php

namespace BadaBoom\Tests\ChainNode\Sender;

use BadaBoom\DataHolder\DataHolder;
use BadaBoom\ChainNode\Sender\Sender;

class SenderTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * 
     * @test
     */
    public function shouldBeExtendedByAbstractChainNode()
    {
        $rc = new \ReflectionClass('BadaBoom\ChainNode\Sender\Sender');
        $this->assertTrue($rc->isSubclassOf('BadaBoom\ChainNode\AbstractChainNode'));
    }

    /**
     * 
     * @test
     */
    public function shouldTakeAdapterAndSerializerInConstructor()
    {
        new Sender($this->createMockAdapter(), $this->createMockSerializer());
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

        $sender = new Sender($this->createMockAdapter(), $serializer);
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

        $sender = new Sender($this->createMockAdapter(), $serializer);
        $sender->setFormat('fake');
    }

    /**
     * 
     * @test
     */
    public function shouldSerializeDataToSetFormatBeforeSend()
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

        $sender = new Sender($this->createMockAdapter(), $serializer);
        $sender->setFormat($format);
        
        $sender->handle($data);
    }

    /**
     * 
     * @test
     */
    public function shouldSendSerializedDataViaAdapter()
    {

    }

    /**
     * 
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function createMockAdapter()
    {
        return $this->getMock('BadaBoom\ChainNode\Sender\SenderAdapterInterface');
    }

    /**
     * 
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function createMockSerializer()
    {
        return $this->getMock('Symfony\Component\Serializer\SerializerInterface');
    }
}