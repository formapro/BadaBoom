<?php

namespace BadaBoom\Adapter\Mailer;

use BadaBoom\Adapter\SenderAdapterInterface;

interface MailerAdapterInterface extends SenderAdapterInterface
{
    /**
     * @param string $from
     * @param array $to
     * @param string $subject
     * @param string $content
     * @param array $additionalHeaders
     * 
     * @return void
     */
    function send($sender, array $recipients, $subject, $content, array $headers = array());
}