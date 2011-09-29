<?php

namespace BadaBoom\Tests\Adapter\Sender;

class MailerAdapterInterfaceTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     * @test
     */
    public function shouldImplementGenericSenderAdapterInterface()
    {
        $rc = new \ReflectionClass('BadaBoom\Adapter\Mailer\MailerAdapterInterface');
        $this->assertTrue($rc->implementsInterface('BadaBoom\Adapter\SenderAdapterInterface'));
    }
}
