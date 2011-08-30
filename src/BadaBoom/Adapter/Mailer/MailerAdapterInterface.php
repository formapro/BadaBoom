<?php

namespace BadaBoom\Adapter\Mailer;

use BadaBoom\Adapter\AdapterInterface;

interface MailerAdapterInterface extends AdapterInterface
{
    function send($from, $to, $subject, $content, array $additionalHeaders = array());
}