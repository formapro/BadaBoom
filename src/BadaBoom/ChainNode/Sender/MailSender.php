<?php

namespace BadaBoom\ChainNode\Sender;

use Symfony\Component\Serializer\SerializerInterface;

use BadaBoom\Adapter\Mailer\MailerAdapterInterface;
use BadaBoom\DataHolder\DataHolderInterface;

class MailSender extends AbstractSender
{
    /**
     * @throws \InvalidArgumentException
     *
     * @param \BadaBoom\Adapter\Mailer\MailerAdapterInterface $adapter
     * @param \Symfony\Component\Serializer\SerializerInterface $serializer
     *
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
    public function handle(\Exception $exception, DataHolderInterface $data)
    {
        $serializedData = $this->serialize($data);
        $subject = $data->get('subject', $this->configuration->get('subject'));

        $this->adapter->send(
            $this->configuration->get('sender'),
            $this->configuration->get('recipients'),
            $subject,
            $serializedData,
            $this->configuration->get('headers', array())
        );

        $this->handleNextNode($exception, $data);
    }

    /**
     * @throws \InvalidArgumentException If the sender does not fit in a mail format
     *
     * @param string $sender
     *
     * @return void
     */
    protected function validateSender($sender)
    {
        if (false == (is_string($sender) && $this->isValidMail($sender))) {
            throw new \InvalidArgumentException('Given sender ' . var_export($sender, true) . ' is invalid');
        }
    }

    /**
     * @throws \InvalidArgumentException if recipients list is empty
     * @throws \InvalidArgumentException if recipients list has at least one invalid recipient
     *
     * @param array $recipients
     *
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

            throw new \InvalidArgumentException(sprintf(
                'Given recipients list %s has invalid recipient %s',
                var_export($recipients, true),
                var_export($recipient, true)
            ));
        }
    }

    /**
     * @param $mail
     *
     * @return boolean
     */
    protected function isValidMail($mail)
    {
        return preg_match('|([a-z0-9_\.\-]{1,20})@([a-z0-9\.\-]{1,20})\.([a-z]{2,4})|is', $mail) ? true : false;
    }
}