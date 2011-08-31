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
     * @expectedExceptionMessage Given recipient
     * @dataProvider invalidRecipientProvider
     */
    public function throwExceptionWhenConstructWithIncorrectRecipient($recipient)
    {
        $configuration = new DataHolder();
        $configuration->set('to', $recipient);

        new MailSender($this->createMockAdapter(), $this->createMockSerializer(), $configuration);
    }

    /**
     * 
     * @test
     */
    public function shouldPassRecipientCheckAndDelegateConstructingToParent()
    {
        $to = 'john@doe.com';
        $format = 'html';

        $serializer = $this->createMockSerializer();
        $serializer->expects($this->once())
            ->method('supportsSerialization')
            ->with($format)
            ->will($this->returnValue(true))
        ;

        $configuration = new DataHolder();
        $configuration->set('format', $format);
        $configuration->set('to', $to);

        new MailSender($this->createMockAdapter(), $serializer, $configuration);
    }

    /**
     *
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
    public static function invalidRecipientProvider()
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
}