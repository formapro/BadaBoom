<?php
namespace BadaBoom\Tests\ChainNode\Sender;

use BadaBoom\DataHolder\DataHolder;
use BadaBoom\Context;

use Symfony\Component\Serializer\Serializer;

class AbstractSenderTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldBeSubClassOfAbstractChainNode()
    {
        $rc = new \ReflectionClass('BadaBoom\ChainNode\Sender\AbstractSender');
        $this->assertTrue($rc->isSubclassOf('BadaBoom\ChainNode\AbstractChainNode'));
    }

    /**
     * @test
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Mandatory field "format" is missing in the given configuration
     */
    public function throwWhenFormatIsNotGivenInConstructor()
    {
        $this->createSenderMock($this->createAdapterMock(), $this->createSerializerMock(), new DataHolder());
    }

    /**
     * @test
     *
     * @depends throwWhenFormatIsNotGivenInConstructor
     */
    public function shouldCheckGivenFormatInConstructor()
    {
        $format = 'html';

        $serializer = $this->createSerializerMock();
        $serializer->expects($this->once())
            ->method('supportsEncoding')
            ->with($format)
            ->will($this->returnValue(true))
        ;

        $configuration = new DataHolder();
        $configuration->set('format', $format);

        $this->createSenderMock($this->createAdapterMock(), $serializer, $configuration);
    }

    /**
     * @test
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage is not supported by serializer
     */
    public function throwWhenTryConstructWithUnsupportedSerializeFormat()
    {
        $format = 'unsupported-format';
        $serializer = $this->createSerializerMock();
        $serializer->expects($this->once())
            ->method('supportsEncoding')
            ->with($format)
            ->will($this->returnValue(false))
        ;

        $configuration = new DataHolder();
        $configuration->set('format', $format);

        $this->createSenderMock($this->createAdapterMock(), $serializer, $configuration);
    }

    /**
     * @test
     */
    public function shouldSerializeDataToSetFormatInConfiguration()
    {
        $context = new Context(new \Exception);
        $format = 'html';

        $configuration = new DataHolder();
        $configuration->set('format', $format);

        $encoder = $this->getMock('Symfony\Component\Serializer\Encoder\EncoderInterface');
        $encoder->expects($this->once())
            ->method('supportsEncoding')
            ->with($format)
            ->will($this->returnValue(true))
        ;
        $serializer = new Serializer(array(), array($format => $encoder));

        $sender = $this->createSenderMock($this->createAdapterMock(), $serializer, $configuration);

        $sender->serialize($context);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function createAdapterMock()
    {
        return $this->getMock('BadaBoom\Adapter\SenderAdapterInterface');
    }

    /**
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function createSerializerMock()
    {
        return $this->getMock('Symfony\Component\Serializer\Serializer');
    }

    /**
     * @param $adapter
     * @param $serializer
     * @param $configuration
     * 
     * @return AbstractSender
     */
    protected function createSenderMock($adapter, $serializer, $configuration)
    {
        $class = $this->getSenderClass();
        return new $class($adapter, $serializer, $configuration);
    }

    /**
     * @return string
     */
    protected function getSenderClass()
    {
        return $this->getMockClass('BadaBoom\ChainNode\Sender\AbstractSender', array('handle'));
    }
}