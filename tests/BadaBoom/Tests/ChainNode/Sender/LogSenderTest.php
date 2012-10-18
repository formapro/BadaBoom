<?php
namespace BadaBoom\Tests\ChainNode\Sender;

use Symfony\Component\Serializer\Serializer;

use BadaBoom\ChainNode\Sender\LogSender;
use BadaBoom\Context;

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
        
        $this->assertEquals('options', $parameters[2]->getName());
    }

    /**
     * @test
     * 
     * @expectedException Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @expectedExceptionMessage Given log_level "not-supported-log-level" is not supported.
     */
    public function throwIfNotSupportedLogLevelGiven()
    {
        $options = array(
            'format' => 'json',
            'log_level' => 'not-supported-log-level'
        );

        $adapter = $this->getMock('BadaBoom\Adapter\Logger\LoggerAdapterInterface');
        $encoder = $this->getMock('Symfony\Component\Serializer\Encoder\EncoderInterface');
        $encoder->expects($this->once())
            ->method('supportsEncoding')
            ->with($options['format'])
            ->will($this->returnValue(true))
        ;
        $serializer = new Serializer(array(), array($options['format'] => $encoder));

        new LogSender($adapter, $serializer, $options);
    }

    /**
     * @test
     */
    public function shouldLogDataWithLevelGivenByContext()
    {
        $options = array(
            'format' => 'json',
            'log_level' => LogSender::ALERT
        );

        $context = new Context(new \Exception);
        $context->setVar('log_level', LogSender::CRITICAL);
        $serializedData = 'Hey! Log me.';

        //guard
        $this->assertNotEquals($options['log_level'], $context->getVar('log_level'));

        $encoder = $this->getMock('Symfony\Component\Serializer\Encoder\EncoderInterface');
        $encoder->expects($this->once())
            ->method('supportsEncoding')
            ->with($options['format'])
            ->will($this->returnValue(true))
        ;
        $encoder->expects($this->once())
            ->method('encode')
            ->with(array('log_level' => LogSender::CRITICAL), $options['format'])
            ->will($this->returnValue($serializedData))
        ;
        $serializer = new Serializer(array(), array($options['format'] => $encoder));

        $adapter = $this->getMock('BadaBoom\Adapter\Logger\LoggerAdapterInterface');
        $adapter->expects($this->once())
            ->method('log')
            ->with($serializedData, $context->getVar('log_level'))
        ;

        $sender = new LogSender($adapter, $serializer, $options);
        $sender->handle($context);
    }

    /**
     * @test
     */
    public function shouldLogDataWithLevelGivenByConfiguration()
    {
        $options = array(
            'format' => 'json',
            'log_level' => LogSender::CRITICAL
        );

        $context = new Context(new \Exception);
        $serializedData = 'Hey! Log me.';

        $encoder = $this->getMock('Symfony\Component\Serializer\Encoder\EncoderInterface');
        $encoder->expects($this->once())->method('supportsEncoding')->will($this->returnValue(true));
        $encoder->expects($this->once())
            ->method('encode')
            ->with(array(), $options['format'])
            ->will($this->returnValue($serializedData))
        ;
        $serializer = new Serializer(array(), array($options['format'] => $encoder));

        $adapter = $this->getMock('BadaBoom\Adapter\Logger\LoggerAdapterInterface');
        $adapter->expects($this->once())
            ->method('log')
            ->with($serializedData, $options['log_level'])
        ;

        $sender = new LogSender($adapter, $serializer, $options);
        $sender->handle($context);
    }

    /**
     * @test
     */
    public function shouldLogDataWithDefaultLevel()
    {
        $options = array(
            'format' => 'json',
        );

        $context = new Context(new \Exception);
        $serializedData = 'Hey! Log me.';

        $encoder = $this->getMock('Symfony\Component\Serializer\Encoder\EncoderInterface');
        $encoder->expects($this->once())->method('supportsEncoding')->will($this->returnValue(true));
        $encoder->expects($this->once())
            ->method('encode')
            ->with(array(), $options['format'])
            ->will($this->returnValue($serializedData))
        ;
        $serializer = new Serializer(array(), array($options['format'] => $encoder));

        $adapter = $this->getMock('BadaBoom\Adapter\Logger\LoggerAdapterInterface');
        $adapter->expects($this->once())
            ->method('log')
            ->with($serializedData, LogSender::INFO)
        ;

        $sender = new LogSender($adapter, $serializer, $options);
        $sender->handle($context);
    }

    /**
     * @param string $supportedFormat
     * 
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function createSerializerMock($supportedFormat)
    {
        $serializer = $this->getMock('Symfony\Component\Serializer\Serializer');
        $serializer->expects($this->any())
            ->method('supportsEncoding')
            ->with($supportedFormat)
            ->will($this->returnValue(true))
        ;

        return $serializer;
    }
}