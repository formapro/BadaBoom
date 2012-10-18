<?php
namespace BadaBoom\Tests\ChainNode\Sender;

use BadaBoom\ChainNode\Sender\MailSender;
use BadaBoom\DataHolder\DataHolder;
use BadaBoom\Context;

use Symfony\Component\Serializer\Serializer;

class MailSenderTest extends \PHPUnit_Framework_TestCase
{
    protected $serializerStub;
    
    /**
     * @test
     */
    public function shouldBeSubClassOfAbstractSender()
    {
        $rc = new \ReflectionClass('BadaBoom\ChainNode\Sender\MailSender');
        $this->assertTrue($rc->isSubclassOf('BadaBoom\ChainNode\Sender\AbstractSender'));
    }

    /**
     * @test
     *
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @expectedExceptionMessage Given sender
     *
     * @dataProvider provideInvalidMail
     */
    public function throwWhenConstructWithIncorrectSender($invalidSender)
    {
        $options = array(
            'format' => 'supported',
            'sender' => $invalidSender,
            'recipients' => 'foo@example.com',
        );

        new MailSender($this->createMockAdapter(), $this->createSerializerStub(), $options);
    }

    /**
     * @test
     *
     * @depends throwWhenConstructWithIncorrectSender
     *
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @expectedExceptionMessage Recipients list should not be empty
     */
    public function throwWhenConstructWithEmptyRecipientsList()
    {
        $options = array(
            'format' => 'supported',
            'sender' => 'valid@sender.com',
            'recipients' => array()
        );

        new MailSender($this->createMockAdapter(), $this->createSerializerStub(), $options);
    }

    /**
     * @test
     *
     * @depends throwWhenConstructWithEmptyRecipientsList
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Given recipient
     *
     * @dataProvider provideInvalidMail
     */
    public function throwWhenConstructWithIncorrectRecipientsList($invalidRecipient)
    {
        $options = array(
            'format' => 'supported',
            'sender' => 'valid@sender.com',
            'recipients' => array($invalidRecipient)
        );

        new MailSender($this->createMockAdapter(), $this->createSerializerStub(), $options);
    }

    /**
     * @test
     * 
     * @depends throwWhenConstructWithIncorrectRecipientsList
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Given recipient
     *
     * @dataProvider provideInvalidMail
     */
    public function throwWhenConstructWithAnyNumberOfIncorrectRecipients($invalidRecipient)
    {
        $options = array(
            'format' => 'supported',
            'sender' => 'valid@sender.com',
            'recipients' => array('valid@recipient.com', $invalidRecipient, 'another_valid@recipient.com')
        );

        new MailSender($this->createMockAdapter(), $this->createSerializerStub(), $options);
    }

    /**
     * @test
     */
    public function shouldPassValidationOfSenderAndRecipientsAndFormatInConstruct()
    {
        $options = array(
            'format' => 'supported',
            'sender' => 'valid@sender.com',
            'recipients' => array('valid@recipient.com')
        );

        new MailSender($this->createMockAdapter(), $this->createSerializerStub(), $options);
    }
//
//    /**
//     * @test
//     */
//    public function shouldSendSerializedContentAccordingGivenConfiguration()
//    {
//        $context = new Context(new \Exception);
//        $serializedData = 'Plain text';
//        $configuration = $this->getFullConfiguration();
//
//        $encoder = $this->getMock('Symfony\Component\Serializer\Encoder\EncoderInterface');
//        $encoder->expects($this->once())
//            ->method('supportsEncoding')
//            ->with($configuration->get('format'))
//            ->will($this->returnValue(true))
//        ;
//        $encoder->expects($this->once())
//            ->method('encode')
//            ->with(array(), $configuration->get('format'))
//            ->will($this->returnValue($serializedData))
//        ;
//        $serializer = new Serializer(array(), array($configuration->get('format') => $encoder));
//
//        $adapter = $this->createMockAdapter();
//        $adapter->expects($this->once())
//            ->method('send')
//            ->with(
//                $configuration->get('sender'),
//                $configuration->get('recipients'),
//                $configuration->get('subject'),
//                $serializedData,
//                $configuration->get('headers')
//            )
//        ;
//        $sender = new MailSender($adapter, $serializer, $configuration);
//
//        $sender->handle($context);
//    }
//
//    /**
//     * @test
//     */
//    public function shouldTakeSubjectFromHandledDataInsteadOfConfigurationValue()
//    {
//        $context = new Context(new \Exception);
//        $context->setVar('subject', 'Hey! There is an error.');
//
//        $configuration = new DataHolder();
//        $configuration->set('format', 'html');
//        $configuration->set('sender', 'valid@sender.com');
//        $configuration->set('recipients', array('john@doe.com'));
//        $configuration->set('subject', 'Static subject from config');
//
//        $encoder = $this->getMock('Symfony\Component\Serializer\Encoder\EncoderInterface');
//        $encoder->expects($this->any())->method('supportsEncoding')->will($this->returnValue(true));
//        $encoder->expects($this->any())->method('encode');
//        $serializer = new Serializer(array(), array($configuration->get('format') => $encoder));
//
//        $adapter = $this->createMockAdapter();
//        $adapter->expects($this->once())
//            ->method('send')
//            ->with(
//                $configuration->get('sender'),
//                $configuration->get('recipients'),
//                $context->getVar('subject'),
//                null,
//                array()
//            )
//        ;
//
//        $sender = new MailSender($adapter, $serializer, $configuration);
//        $sender->handle($context);
//    }
//
//    /**
//     * @test
//     */
//    public function shouldSendEmptySubjectIfItWasNotSetBefore()
//    {
//        $emptySubject = null;
//        $context = new Context(new \Exception);
//
//        $configuration = new DataHolder();
//        $configuration->set('format', 'html');
//        $configuration->set('sender', 'valid@sender.com');
//        $configuration->set('recipients', array('john@doe.com'));
//        $configuration->set('headers', array('BB' => 'support@site.com'));
//
//        $encoder = $this->getMock('Symfony\Component\Serializer\Encoder\EncoderInterface');
//        $encoder->expects($this->any())->method('supportsEncoding')->will($this->returnValue(true));
//        $encoder->expects($this->any())->method('encode');
//        $serializer = new Serializer(array(), array($configuration->get('format') => $encoder));
//
//        $adapter = $this->createMockAdapter();
//        $adapter->expects($this->once())
//            ->method('send')
//            ->with(
//                $configuration->get('sender'),
//                $configuration->get('recipients'),
//                $emptySubject,
//                null,
//                $configuration->get('headers')
//            )
//        ;
//
//        $sender = new MailSender($adapter, $serializer, $configuration);
//        $sender->handle($context);
//    }
//
//    /**
//     * @test
//     */
//    public function shouldSendEmptyHeadersListIfItWasNotSetBefore()
//    {
//        $emptyHeaderList = array();
//        $context = new Context(new \Exception);
//
//        $configuration = new DataHolder();
//        $configuration->set('format', 'html');
//        $configuration->set('sender', 'valid@sender.com');
//        $configuration->set('recipients', array('john@doe.com'));
//
//        $encoder = $this->getMock('Symfony\Component\Serializer\Encoder\EncoderInterface');
//        $encoder->expects($this->any())->method('supportsEncoding')->will($this->returnValue(true));
//        $encoder->expects($this->any())->method('encode');
//        $serializer = new Serializer(array(), array($configuration->get('format') => $encoder));
//
//        $adapter = $this->createMockAdapter();
//        $adapter->expects($this->once())
//            ->method('send')
//            ->with(
//                $configuration->get('sender'),
//                $configuration->get('recipients'),
//                null,
//                null,
//                $emptyHeaderList
//            )
//        ;
//
//        $sender = new MailSender($adapter, $serializer, $configuration);
//        $sender->handle($context);
//    }
//
//    /**
//     * @test
//     */
//    public function shouldSendMailAndDelegateHandlingToNextChainNode()
//    {
//        $context = new Context(new \Exception);
//        $configuration = $this->getFullConfiguration();
//
//        $encoder = $this->getMock('Symfony\Component\Serializer\Encoder\EncoderInterface');
//        $encoder->expects($this->any())->method('supportsEncoding')->will($this->returnValue(true));
//        $encoder->expects($this->any())->method('encode');
//        $serializer = new Serializer(array(), array($configuration->get('format') => $encoder));
//
//        $adapter = $this->createMockAdapter();
//        $adapter->expects($this->any())->method('send');
//
//        $nextChainNode = $this->createMockChainNode();
//        $nextChainNode
//            ->expects($this->once())
//            ->method('handle')
//            ->with($context)
//        ;
//
//        $sender = new MailSender($adapter, $serializer, $configuration);
//        $sender->nextNode($nextChainNode);
//        
//        $sender->handle($context);
//    }

    /**
     * @return \BadaBoom\DataHolder\DataHolder
     */
    protected function getFullConfiguration()
    {
        $configuration = new DataHolder();
        $configuration->set('format', 'html');
        $configuration->set('sender', 'valid@sender.com');
        $configuration->set('subject', 'It should be provided via SubjectProvider');
        $configuration->set('recipients', array('john@doe.com'));
        $configuration->set('headers', array('BB' => 'support@site.com'));

        return $configuration;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function createMockAdapter()
    {
        return $this->getMock('BadaBoom\Adapter\Mailer\MailerAdapterInterface');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function createMockSerializer()
    {
        return $this->getMock('Symfony\Component\Serializer\Serializer');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function createSerializerStub()
    {
        $serializerStub = $this->createMockSerializer();
        
        $serializerStub
            ->expects($this->any())
            ->method('supportsEncoding')
            ->with('supported')
            ->will($this->returnValue(true))
        ;
        
        return $serializerStub;
    }

    /**
     * @static
     *
     * @return array
     */
    public static function provideInvalidMail()
    {
        return array(
            array(' '),
            array(''),
            array('john@'),
            array('name@domain.r'),
            array('name@.ru'),
            array('@domain.com'),
            array(1),
            array(1.2),
            array(false),
            array(true),
            array(null),
            array(new \stdClass),
            array(array()),
        );
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function createMockChainNode()
    {
        return $this->getMockForAbstractClass('BadaBoom\ChainNode\AbstractChainNode');
    }
}