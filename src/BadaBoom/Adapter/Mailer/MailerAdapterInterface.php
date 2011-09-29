<?php

namespace BadaBoom\Adapter\Mailer;

use BadaBoom\Adapter\SenderAdapterInterface;

interface MailerAdapterInterface extends SenderAdapterInterface
{
    /**
     * @abstract
     * @param string $from
     * @param array $to
     * @param string $subject
     * @param string $content
     * @param array $additionalHeaders
     * @return void
     */
    function send($from, array $to, $subject, $content, array $additionalHeaders = array());
}