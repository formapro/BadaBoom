<?php
namespace BadaBoom\Tests\ChainNode\Sender;

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
     * @expectedException \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     * @expectedExceptionMessage The required option "format" is  missing.
     */
    public function throwWhenFormatIsNotGivenInConstructor()
    {
        $this->createSenderMock($this->createAdapterMock(), $this->createSerializerMock(), array());
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

        $this->createSenderMock($this->createAdapterMock(), $serializer, array(
            'format' => $format
        ));
    }

    /**
     * @test
     *
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * 
     * @expectedExceptionMessage Given format "unsupported-format" is not supported by serializer
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

        $this->createSenderMock($this->createAdapterMock(), $serializer, array(
            'format' => $format
        ));
    }

    /**
     * @test
     */
    public function shouldSerializeDataToSetFormatInOptions()
    {
        $context = new Context(new \Exception);
        $format = 'html';

        $encoder = $this->getMock('Symfony\Component\Serializer\Encoder\EncoderInterface');
        $encoder->expects($this->once())
            ->method('supportsEncoding')
            ->with($format)
            ->will($this->returnValue(true))
        ;
        $encoder->expects($this->once())
            ->method('encode')
            ->with($this->isType('array'), $format)
            ->will($this->returnValue('encoded'))
        ;
        
        $serializer = new Serializer(array(), array($format => $encoder));

        $sender = $this->createSenderMock($this->createAdapterMock(), $serializer, array(
            'format' => $format,
        ));

        $result = $sender->serialize($context);
        
        $this->assertEquals('encoded', $result);
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
     * @param $options
     * 
     * @return AbstractSender
     */
    protected function createSenderMock($adapter, $serializer, $options)
    {
        $class = $this->getSenderClass();
        return new $class($adapter, $serializer, $options);
    }

    /**
     * @return string
     */
    protected function getSenderClass()
    {
        return $this->getMockClass('BadaBoom\ChainNode\Sender\AbstractSender', array('handle'));
    }
}