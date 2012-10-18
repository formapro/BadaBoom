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

    /**
     * @test
     */
    public function shouldSendSerializedContentAccordingGivenOptions()
    {
        $context = new Context(new \Exception);
        $serializedData = 'Plain text';
        $options = $this->getAllPossibleOptions();

        $encoder = $this->getMock('Symfony\Component\Serializer\Encoder\EncoderInterface');
        $encoder->expects($this->once())
            ->method('supportsEncoding')
            ->with($options['format'])
            ->will($this->returnValue(true))
        ;
        $encoder->expects($this->once())
            ->method('encode')
            ->with(array(), $options['format'])
            ->will($this->returnValue($serializedData))
        ;
        $serializer = new Serializer(array(), array($options['format'] => $encoder));

        $adapter = $this->createMockAdapter();
        $adapter->expects($this->once())
            ->method('send')
            ->with(
                $options['sender'],
                $options['recipients'],
                $options['subject'],
                $serializedData,
                $options['headers']
            )
        ;
        $sender = new MailSender($adapter, $serializer, $options);

        $sender->handle($context);
    }

    /**
     * @test
     */
    public function shouldTakeSubjectFromHandledDataInsteadOfConfigurationValue()
    {
        $context = new Context(new \Exception);
        $context->setVar('subject', 'Hey! There is an error.');

        $options = $this->getAllPossibleOptions();
        $options['subject'] = 'Static subject from config';
        $options['headers'] = array();

        $encoder = $this->getMock('Symfony\Component\Serializer\Encoder\EncoderInterface');
        $encoder->expects($this->any())->method('supportsEncoding')->will($this->returnValue(true));
        $encoder->expects($this->any())->method('encode');
        $serializer = new Serializer(array(), array($options['format'] => $encoder));

        $adapter = $this->createMockAdapter();
        $adapter->expects($this->once())
            ->method('send')
            ->with(
                $options['sender'],
                $options['recipients'],
                $context->getVar('subject'),
                null,
                array()
            )
        ;

        $sender = new MailSender($adapter, $serializer, $options);
        $sender->handle($context);
    }

    /**
     * @test
     */
    public function shouldSendEmptySubjectIfItWasNotSetBefore()
    {
        $emptySubject = null;
        $context = new Context(new \Exception);

        $options = $this->getAllPossibleOptions();
        unset($options['subject']);

        $encoder = $this->getMock('Symfony\Component\Serializer\Encoder\EncoderInterface');
        $encoder->expects($this->any())->method('supportsEncoding')->will($this->returnValue(true));
        $encoder->expects($this->any())->method('encode');
        $serializer = new Serializer(array(), array($options['format'] => $encoder));

        $adapter = $this->createMockAdapter();
        $adapter->expects($this->once())
            ->method('send')
            ->with(
                $options['sender'],
                $options['recipients'],
                $emptySubject,
                null,
                $options['headers']
            )
        ;

        $sender = new MailSender($adapter, $serializer, $options);
        $sender->handle($context);
    }

    /**
     * @test
     */
    public function shouldSendEmptyHeadersListIfItWasNotSetBefore()
    {
        $emptyHeaderList = array();
        $context = new Context(new \Exception);

        $options = $this->getAllPossibleOptions();
        unset($options['headers']);
        unset($options['subject']);

        $encoder = $this->getMock('Symfony\Component\Serializer\Encoder\EncoderInterface');
        $encoder->expects($this->any())->method('supportsEncoding')->will($this->returnValue(true));
        $encoder->expects($this->any())->method('encode');
        $serializer = new Serializer(array(), array($options['format'] => $encoder));

        $adapter = $this->createMockAdapter();
        $adapter->expects($this->once())
            ->method('send')
            ->with(
                $options['sender'],
                $options['recipients'],
                null,
                null,
                $emptyHeaderList
            )
        ;

        $sender = new MailSender($adapter, $serializer, $options);
        $sender->handle($context);
    }

    /**
     * @test
     */
    public function shouldSendMailAndDelegateHandlingToNextChainNode()
    {
        $context = new Context(new \Exception);
        
        $options = $this->getAllPossibleOptions();

        $encoder = $this->getMock('Symfony\Component\Serializer\Encoder\EncoderInterface');
        $encoder->expects($this->any())->method('supportsEncoding')->will($this->returnValue(true));
        $encoder->expects($this->any())->method('encode');
        $serializer = new Serializer(array(), array($options['format'] => $encoder));

        $adapter = $this->createMockAdapter();
        $adapter->expects($this->any())->method('send');

        $nextChainNode = $this->createMockChainNode();
        $nextChainNode
            ->expects($this->once())
            ->method('handle')
            ->with($context)
        ;

        $sender = new MailSender($adapter, $serializer, $options);
        $sender->nextNode($nextChainNode);
        
        $sender->handle($context);
    }

    /**
     * @return array
     */
    protected function getAllPossibleOptions()
    {
        return array(
            'format' => 'html',
            'sender' => 'valid@sender.com',
            'subject' => 'It should be provided via SubjectProvider',
            'recipients' => array('john@doe.com'),
            'headers' => array('BB' => 'support@site.com'),
        );
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