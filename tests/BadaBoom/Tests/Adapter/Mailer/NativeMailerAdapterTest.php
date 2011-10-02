<?php

namespace BadaBoom\Tests\Adapter\Mailer;

require 'FunctionStubHelper.php';

use BadaBoom\Tests\FunctionCallbackRegistry;
use BadaBoom\Adapter\Mailer\NativeMailerAdapter;

class NativeMailerAdapterTestCase extends \PHPUnit_Framework_TestCase
{
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

        $receivedArguments = array();
        $functionCalls = 0;
        FunctionCallbackRegistry::getInstance()->registerCallback('mail', function() use(&$receivedArguments, &$functionCalls){
            $receivedArguments = func_get_args();
            $functionCalls++;
        });

        $adapter = new NativeMailerAdapter();
        $adapter->send($from, $to, $subject, $content);

        $this->assertEquals(1, $functionCalls);

        $this->assertEquals($to[0], $receivedArguments[0]);
        $this->assertEquals($subject, $receivedArguments[1]);
        $this->assertEquals($content, $receivedArguments[2]);
        $this->assertContains(
            sprintf("From: %s \r\n", $from),
            $receivedArguments[3]
        );
    }

    /**
     * @test
     *
     * @depends shouldSendOneMailThroughNativeFunction
     */
    public function shouldSendOneMailWithAdditionalHeadersViaNativeFunction()
    {
        $headers = array('reply-to' => 'another');

        $receivedArguments = array();
        $functionCalls = 0;
        FunctionCallbackRegistry::getInstance()->registerCallback('mail', function() use(&$receivedArguments, &$functionCalls){
            $receivedArguments = func_get_args();
            $functionCalls++;
        });

        $adapter = new NativeMailerAdapter();
        $adapter->send('from', array('to'), 'subject', 'content', $headers);

        $this->assertContains(
            sprintf("Reply-To: %s \r\n", $headers['reply-to']),
            $receivedArguments[3]
        );
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

        $receivedArguments = array();
        $functionCalls = 0;
        FunctionCallbackRegistry::getInstance()->registerCallback('mail', function() use(&$receivedArguments, &$functionCalls){
            $args = func_get_args();
            $receivedArguments[] = array(
                'to'      => $args[0],
                'subject' => $args[1],
                'content' => $args[2],
                'headers' => $args[3],
            );
            $functionCalls++;
        });

        $adapter = new NativeMailerAdapter();
        $adapter->send($from, $to, $subject, $content);

        $this->assertEquals(count($to), $functionCalls);

        $this->assertNotEquals($receivedArguments[0]['to'], $receivedArguments[1]['to']);
        $this->assertEquals($to[0], $receivedArguments[0]['to']);
        $this->assertEquals($to[1], $receivedArguments[1]['to']);

        $this->assertEquals($receivedArguments[0]['subject'], $receivedArguments[1]['subject']);
        $this->assertEquals($subject, $receivedArguments[0]['subject']);

        $this->assertEquals($receivedArguments[0]['content'], $receivedArguments[1]['content']);
        $this->assertEquals($content, $receivedArguments[0]['content']);

        $this->assertEquals($receivedArguments[0]['headers'], $receivedArguments[1]['headers']);
        $this->assertContains(
            sprintf("From: %s \r\n", $from),
            $receivedArguments[0]['headers']
        );
    }

    /**
     * @test
     *
     * @depends shouldSendMailsAsManyTimesAsThereAreRecipients
     */
    public function shouldSendMailsWithAdditionalHeadersAsManyTimesAsThereAreRecipients()
    {
        $to = array('you', 'we', 'they');
        $headers = array('reply-to' => 'another');

        $receivedArguments = array();
        $functionCalls = 0;
        FunctionCallbackRegistry::getInstance()->registerCallback('mail', function() use(&$receivedArguments, &$functionCalls){
            $args = func_get_args();
            $receivedArguments[] = array(
                'headers' => $args[3],
            );
            $functionCalls++;
        });

        $adapter = new NativeMailerAdapter();
        $adapter->send('from', $to, 'subject', 'content', $headers);

        $this->assertEquals(count($to), $functionCalls);

        $this->assertContains(
            sprintf("Reply-To: %s \r\n", $headers['reply-to']),
            $receivedArguments[0]['headers']
        );

        $this->assertContains(
            sprintf("Reply-To: %s \r\n", $headers['reply-to']),
            $receivedArguments[1]['headers']
        );

        $this->assertContains(
            sprintf("Reply-To: %s \r\n", $headers['reply-to']),
            $receivedArguments[2]['headers']
        );
    }
}