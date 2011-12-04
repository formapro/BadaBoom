<?php

namespace BadaBoom\Tests\Adapter\Mailer;

use Fumocker\Fumocker;

use BadaBoom\Tests\FunctionCallbackRegistry;
use BadaBoom\Adapter\Mailer\NativeMailerAdapter;

class NativeMailerAdapterTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Fumocker\Fumocker
     */
    protected $fumocker;

    public function setUp()
    {
        $this->fumocker = new Fumocker;
    }

    /**
     * @return void
     */
    public function tearDown()
    {
        $this->fumocker->cleanup();
    }

    /**
     * @test
     */
    public function shouldImplementMailerAdapterInterface()
    {
        $rc = new \ReflectionClass('BadaBoom\Adapter\Mailer\NativeMailerAdapter');
        $this->assertTrue($rc->implementsInterface('BadaBoom\Adapter\Mailer\MailerAdapterInterface'));
    }

    /**
     * @test
     */
    public function shouldSendOneMailThroughNativeFunction()
    {
        $from = 'me';
        $to = array('you');
        $subject = 'Mail subject';
        $content = 'Hey! You have an error at line #1';

        $mail_mock = $this->fumocker->getMock('BadaBoom\Adapter\Mailer', 'mail');
        $mail_mock
            ->expects($this->once())
            ->method('mail')
            ->with(
                $this->equalTo($to[0]),
                $this->equalTo($subject),
                $this->equalTo($content),
                $this->stringContains(sprintf("From: %s \r\n", $from))
            )
        ;

        $adapter = new NativeMailerAdapter();
        $adapter->send($from, $to, $subject, $content);
    }

    /**
     * @test
     *
     * @depends shouldSendOneMailThroughNativeFunction
     */
    public function shouldSendOneMailWithAdditionalHeadersViaNativeFunction()
    {
        $headers = array('reply-to' => 'another');

        $mail_mock = $this->fumocker->getMock('BadaBoom\Adapter\Mailer', 'mail');
        $mail_mock
            ->expects($this->once())
            ->method('mail')
            ->with(
                $this->anything(),
                $this->anything(),
                $this->anything(),
                $this->stringContains(sprintf("Reply-To: %s \r\n", $headers['reply-to']))
            )
        ;

        $adapter = new NativeMailerAdapter();
        $adapter->send('from', array('to'), 'subject', 'content', $headers);
    }

    /**
     * @test
     */
    public function shouldSendMailsAsManyTimesAsThereAreRecipients()
    {
        $from = 'me';
        $to = array('you', 'we', 'they');
        $subject = 'Mail subject';
        $content = 'Hey! You have an error at line #1';

        $mail_mock = $this->fumocker->getMock('BadaBoom\Adapter\Mailer', 'mail');
        $mail_mock
            ->expects($this->exactly(3))
            ->method('mail')
            ->with(
                $this->logicalOr(
                    $this->equalTo($to[0]),
                    $this->equalTo($to[1]),
                    $this->equalTo($to[2])
                ),
                $this->equalTo($subject),
                $this->equalTo($content),
                $this->stringContains(sprintf("From: %s \r\n", $from))
            )
        ;

        $adapter = new NativeMailerAdapter();
        $adapter->send($from, $to, $subject, $content);
    }

    /**
     * @test
     *
     * @depends shouldSendMailsAsManyTimesAsThereAreRecipients
     */
    public function shouldSendMailsWithAdditionalHeadersAsManyTimesAsThereAreRecipients()
    {
        $from = 'me';
        $to = array('you', 'we', 'they');
        $subject = 'Mail subject';
        $content = 'Hey! You have an error at line #1';
        $headers = array('reply-to' => 'another');

        $mail_mock = $this->fumocker->getMock('BadaBoom\Adapter\Mailer', 'mail');
        $mail_mock
            ->expects($this->exactly(3))
            ->method('mail')
            ->with(
                $this->logicalOr(
                    $this->equalTo($to[0]),
                    $this->equalTo($to[1]),
                    $this->equalTo($to[2])
                ),
                $this->equalTo($subject),
                $this->equalTo($content),
                $this->stringContains(sprintf("Reply-To: %s \r\n", $headers['reply-to']))
            )
        ;

        $adapter = new NativeMailerAdapter();
        $adapter->send($from, $to, $subject, $content, $headers);
    }
}