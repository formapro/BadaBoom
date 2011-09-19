<?php

namespace BadaBoom\Tests\ChainNode\Sender;

use BadaBoom\ChainNode\Sender\MailSender;
use BadaBoom\DataHolder\DataHolder;

class MailSenderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * 
     * @test
     */
    public function shouldBeExtendedByAbstractSender()
    {
        $rc = new \ReflectionClass('BadaBoom\ChainNode\Sender\MailSender');
        $this->assertTrue($rc->isSubclassOf('BadaBoom\ChainNode\Sender\AbstractSender'));
    }

    /**
     *
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Given sender
     * @dataProvider invalidMailProvider
     */
    public function throwExceptionWhenConstructWithIncorrectSender($sender)
    {
        $configuration = new DataHolder();
        $configuration->set('sender', $sender);

        new MailSender($this->createMockAdapter(), $this->createMockSerializer(), $configuration);
    }

    /**
     *
     * @test
     * @depends throwExceptionWhenConstructWithIncorrectSender
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Recipients list should not be empty
     */
    public function throwExceptionWhenConstructWithEmptyRecipientsList()
    {
        $configuration = new DataHolder();
        $configuration->set('sender', 'valid@sender.com');
        $configuration->set('recipients', array());

        new MailSender($this->createMockAdapter(), $this->createMockSerializer(), $configuration);
    }

    /**
     *
     * @test
     * @depends throwExceptionWhenConstructWithEmptyRecipientsList
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Given recipient
     * @dataProvider invalidMailProvider
     */
    public function throwExceptionWhenConstructWithIncorrectRecipientsList($recipient)
    {
        $configuration = new DataHolder();
        $configuration->set('sender', 'valid@sender.com');
        $configuration->set('recipients', array($recipient));

        new MailSender($this->createMockAdapter(), $this->createMockSerializer(), $configuration);
    }

    /**
     *
     * @test
     * @depends throwExceptionWhenConstructWithIncorrectRecipientsList
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Given recipient
     * @dataProvider invalidMailProvider
     */
    public function throwExceptionWhenConstructWithAnyNumberOfIncorrectRecipients($recipient)
    {
        $configuration = new DataHolder();
        $configuration->set('sender', 'valid@sender.com');
        $configuration->set('recipients', array('valid@recipient.com', $recipient, 'another_valid@recipient.com'));

        new MailSender($this->createMockAdapter(), $this->createMockSerializer(), $configuration);
    }

    /**
     * @test
     */
    public function shouldPassSenderAndRecipientsCheckAndDelegateConstructingToParent()
    {
        $sender = 'valid@recipient.com';
        $recipient = array('john@doe.com');
        $format = 'html';

        $serializer = $this->createMockSerializer();
        $serializer->expects($this->once())
            ->method('supportsSerialization')
            ->with($format)
            ->will($this->returnValue(true))
        ;

        $configuration = new DataHolder();
        $configuration->set('format', $format);
        $configuration->set('sender', $sender);
        $configuration->set('recipients', $recipient);

        new MailSender($this->createMockAdapter(), $serializer, $configuration);
    }

    /**
     * @test
     */
    public function shouldSendSerializedContentAccordingGivenConfiguration()
    {
        $data = new DataHolder();
        $serializedData = 'Plain text';
        $configuration = $this->getFullConfiguration();

        $serializer = $this->createMockSerializer();
        $serializer->expects($this->once())
            ->method('supportsSerialization')
            ->with($configuration->get('format'))
            ->will($this->returnValue(true))
        ;
        $serializer->expects($this->once())
            ->method('serialize')
            ->with($data, $configuration->get('format'))
            ->will($this->returnValue($serializedData));

        $adapter = $this->createMockAdapter();
        $adapter->expects($this->once())
            ->method('send')
            ->with(
                $configuration->get('sender'),
                $configuration->get('recipients'),
                $configuration->get('subject'),
                $serializedData,
                $configuration->get('headers')
            )
        ;
        $sender = new MailSender($adapter, $serializer, $configuration);
        $sender->handle($data);
    }

    /**
     * @test
     */
    public function shouldSendMailAndDelegateHandlingToNextChainNode()
    {
        $data = new DataHolder();
        $serializedData = 'Plain text';
        $configuration = $this->getFullConfiguration();

        $serializer = $this->createMockSerializer();
        $serializer->expects($this->once())
            ->method('supportsSerialization')
            ->with($configuration->get('format'))
            ->will($this->returnValue(true))
        ;
        $serializer->expects($this->once())
            ->method('serialize')
            ->with($data, $configuration->get('format'))
            ->will($this->returnValue($serializedData));

        $adapter = $this->createMockAdapter();
        $adapter->expects($this->once())
            ->method('send')
            ->with(
                $configuration->get('sender'),
                $configuration->get('recipients'),
                $configuration->get('subject'),
                $serializedData,
                $configuration->get('headers')
            )
        ;

        $nextChainNode = $this->createMockChainNode();
        $nextChainNode->expects($this->once())
            ->method('handle')
            ->with($this->equalTo($data))
        ;

        $sender = new MailSender($adapter, $serializer, $configuration);
        $sender->nextNode($nextChainNode);
        $sender->handle($data);
    }

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
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function createMockSerializer()
    {
        return $this->getMock('Symfony\Component\Serializer\SerializerInterface');
    }

    /**
     * @static
     * @return array
     */
    public static function invalidMailProvider()
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