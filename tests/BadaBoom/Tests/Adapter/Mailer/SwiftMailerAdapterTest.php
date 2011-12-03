<?php

namespace BadaBoom\Tests\Adapter\Mailer;

if (false == class_exists('Swift') && file_exists(__DIR__.'/../../../../../vendor/swiftmailer/swiftmailer/lib/swift_required.php')) {
    require_once __DIR__.'/../../../../../vendor/swiftmailer/swiftmailer/lib/swift_required.php';
}

use BadaBoom\Adapter\Mailer\SwiftMailerAdapter;

class SwiftMailerAdapterCaseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        if(false == class_exists('Swift')) {
            $this->markTestSkipped('Swiftmailer was not loaded.');
        }
    }


    /**
     * @test
     */
    public function shouldImplementMailerAdapterInterface()
    {
        $rc = new \ReflectionClass('BadaBoom\Adapter\Mailer\SwiftMailerAdapter');
        $this->assertTrue($rc->implementsInterface('BadaBoom\Adapter\Mailer\MailerAdapterInterface'));
    }

    /**
     * @test
     */
    public function shouldTakeMailerInstanceInConstructor()
    {
        new SwiftmailerAdapter($this->createSwiftMailerMock());
    }

    /**
     * @test
     */
    public function shouldSendOneMailThroughSwiftMailer()
    {
        $from = 'me@localhost';
        $to = array('you@localhost');
        $subject = 'Mail subject';
        $content = 'Hey! You have an error at line #1';

        $mail = null;
        $mailer = $this->createSwiftMailerMock();
        $mailer->expects($this->once())
            ->method('send')
            ->will($this->returnCallback(function($message)use(&$mail){
                $mail = $message;
            }));
        ;

        $adapter = new SwiftmailerAdapter($mailer);
        $adapter->send($from, $to, $subject, $content);

        $this->assertEquals(array($from => null), $mail->getFrom());
        $this->assertEquals(array($to[0] => null), $mail->getTo());
        $this->assertEquals($subject, $mail->getSubject());
        $this->assertEquals($content, $mail->getBody());
    }

    /**
     * @test
     *
     * @depends shouldSendOneMailThroughSwiftMailer
     */
    public function shouldSendOneMailWithAdditionalHeadersViaSwiftMailer()
    {
        $headers = array('reply-to' => 'another');

        $mail = null;
        $mailer = $this->createSwiftMailerMock();
        $mailer->expects($this->once())
            ->method('send')
            ->will($this->returnCallback(function($message)use(&$mail){
                $mail = $message;
            }));
        ;

        $adapter = new SwiftMailerAdapter($mailer);
        $adapter->send(
            'from@localhost',
            array('to@localhost'),
            'subject',
            'content',
            $headers
        );

        $this->assertEquals(
            $headers['reply-to'],
            $mail->getHeaders()->get('reply-to')->getFieldBody()
        );
    }

    /**
     * @test
     */
    public function shouldSendMailToSeveralRecipients()
    {
        $recipients = array('me@localhost', 'you@localhost');

        $mail = null;
        $mailer = $this->createSwiftMailerMock();
        $mailer->expects($this->once())
            ->method('send')
            ->will($this->returnCallback(function($message)use(&$mail){
                $mail = $message;
            }));
        ;

        $adapter = new SwiftmailerAdapter($mailer);
        $adapter->send(
            'from@localhost',
            $recipients,
            'subject',
            'content'
        );

        $this->assertEquals(
            array($recipients[0] => null, $recipients[1] => null),
            $mail->getTo()
        );
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    public function createSwiftMailerMock()
    {
        return $this->getMock('Swift_Mailer', array(), array(), '', false);
    }
}
