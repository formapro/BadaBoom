<?php

namespace BadaBoom\ChainNode\Sender;

use BadaBoom\Adapter\Mailer\MailerAdapterInterface;
use Symfony\Component\Serializer\SerializerInterface;
use BadaBoom\DataHolder\DataHolderInterface;

class MailSender extends AbstractSender
{
    /**
     * @throws \InvalidArgumentException
     * @param \BadaBoom\Adapter\Mailer\MailerAdapterInterface $adapter
     * @param \Symfony\Component\Serializer\SerializerInterface $serializer
     * @param \BadaBoom\DataHolder\DataHolderInterface $configuration
     */
    public function __construct(MailerAdapterInterface $adapter, SerializerInterface $serializer, DataHolderInterface $configuration)
    {
        $this->validateSender($configuration->get('sender'));
        $this->validateRecipients($configuration->get('recipients'));

        parent::__construct($adapter, $serializer, $configuration);
    }

    /**
     * {@inheritdoc}
     */
    public function handle(DataHolderInterface $data)
    {
        $serializedData = $this->serialize($data);
        $this->adapter->send(
            $this->configuration->get('sender'),
            $this->configuration->get('recipients'),
            $this->configuration->get('subject'),
            $serializedData,
            $this->configuration->get('headers')
        );
        $this->handleNextNode($data);
    }

    /**
     * @throws \InvalidArgumentException
     * @param string $sender
     * @return void
     */
    protected function validateSender($sender)
    {
        if (true == empty($sender) || false == is_string($sender) || false == $this->isValidMail($sender)) {
            throw new \InvalidArgumentException('Given sender ' . var_export($sender, true) . ' is invalid');
        }
    }

    /**
     * @throws \InvalidArgumentException
     * @param array $recipients
     * @return void
     */
    protected function validateRecipients(array $recipients)
    {
        if (true == empty($recipients)) {
            throw new \InvalidArgumentException('Recipients list should not be empty');
        }

        foreach ($recipients as $recipient) {
            if (true == is_string($recipient) && $this->isValidMail($recipient)) {
                continue;
            }
            throw new \InvalidArgumentException('Given recipient list ' . var_export($recipient, true) . ' is invalid');
        }
    }

    /**
     * @param $mail
     * @return boolean
     */
    protected function isValidMail($mail)
    {
        return preg_match('|([a-z0-9_\.\-]{1,20})@([a-z0-9\.\-]{1,20})\.([a-z]{2,4})|is', $mail) ? true : false;
    }
}
