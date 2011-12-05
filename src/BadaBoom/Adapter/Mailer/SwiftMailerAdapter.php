<?php

namespace BadaBoom\Adapter\Mailer;

class SwiftMailerAdapter implements MailerAdapterInterface
{
    /**
     * @var \Swift_Mailer
     */
    protected $mailer;

    /**
     * @param Swift_Mailer $mailer
     */
    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * {@inheritdoc}
     */
    public function send($sender, array $recipients, $subject, $content, array $headers = array())
    {
        $message = \Swift_Message::newInstance($subject, $content)
            ->setFrom($sender)
            ->setTo($recipients);

        $messageHeaders = $message->getHeaders();
        foreach ($headers as $name => $value) {
            $messageHeaders->addTextHeader($name, $value);
        }

        $this->mailer->send($message);
    }
}