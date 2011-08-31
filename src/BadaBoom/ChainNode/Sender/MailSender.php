<?php

namespace BadaBoom\ChainNode\Sender;

use BadaBoom\Adapter\Mailer\MailerAdapterInterface;
use Symfony\Component\Serializer\SerializerInterface;
use BadaBoom\DataHolder\DataHolderInterface;

class MailSender extends AbstractSender
{
    public function __construct(MailerAdapterInterface $adapter, SerializerInterface $serializer, DataHolderInterface $configuration)
    {
        if (false == $this->isValidMail($configuration->get('to'))) {
            throw new \InvalidArgumentException(
                'Given recipient '.var_export($configuration->get('to'), true).' is invalid'
            );
        }

        parent::__construct($adapter, $serializer, $configuration);
    }

    public function handle(DataHolderInterface $data)
    {
        
    }

    /**
     * 
     * @param $mail
     * @return bool
     */
    protected function isValidMail($mail)
    {
        if(true == is_string($mail)) {
            return preg_match('|([a-z0-9_\.\-]{1,20})@([a-z0-9\.\-]{1,20})\.([a-z]{2,4})|is', $mail) ? true : false;
        }

        return false;
    }
}
