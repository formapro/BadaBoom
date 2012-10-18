<?php
namespace BadaBoom\ChainNode\Sender;

use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

use BadaBoom\Adapter\Mailer\MailerAdapterInterface;
use BadaBoom\Context;

class MailSender extends AbstractSender
{
    /**
     * @throws \InvalidArgumentException
     *
     * @param \BadaBoom\Adapter\Mailer\MailerAdapterInterface $adapter
     * @param \Symfony\Component\Serializer\SerializerInterface $serializer
     * @param array $options
     */
    public function __construct(MailerAdapterInterface $adapter, SerializerInterface $serializer, array $options)
    {
        parent::__construct($adapter, $serializer, $options);
    }
    
    protected function getOptionResolver()
    {
        $resolver = parent::getOptionResolver();
        
        $resolver->setDefaults(array(
            'subject' => 'An error occurred.',
            'headers' => array(),
        ));
        
        $resolver->setRequired(array('sender', 'recipients'));
        
        $resolver->setNormalizers(array(
            'sender' => $this->getSenderNormalizer(), 
            'recipients' => $this->getRecipientsNormalizer(),
        ));
        
        return $resolver;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Context $context)
    {
        $this->adapter->send(
            $this->options['sender'],
            $this->options['recipients'],
            $context->getVar('subject', $this->options['subject']),
            $this->serialize($context),
            $this->options['headers']
        );

        $this->handleNextNode($context);
    }

    /**
     * @return \Closure
     */
    protected function getSenderNormalizer()
    {
        return function(Options $options, $value) {
            if (false == is_string($value)) {
                throw new InvalidOptionsException('Given sender ' . var_export($value, true) . ' is not a string.');
            }
            if (false == preg_match('|([a-z0-9_\.\-]{1,20})@([a-z0-9\.\-]{1,20})\.([a-z]{2,4})|is', $value)) {
                throw new InvalidOptionsException('Given sender ' . var_export($value, true) . ' is not a valid email.');
            }
            
            return $value;
        };
    }

    /**
     * @return \Closure
     */
    protected function getRecipientsNormalizer()
    {
        return function(Options $options, $value) {
            if (empty($value)) {
                throw new InvalidOptionsException('Recipients list should not be empty.');
            }
            if (false == is_array($value)) {
                $value = array($value);
            }
            
            foreach ($value as $recipient) {
                if (false == is_string($recipient)) {
                    throw new InvalidOptionsException('Given recipient ' . var_export($recipient, true) . ' is not a string.');
                }
                if (false == preg_match('|([a-z0-9_\.\-]{1,20})@([a-z0-9\.\-]{1,20})\.([a-z]{2,4})|is', $recipient)) {
                    throw new InvalidOptionsException('Given recipient ' . var_export($recipient, true) . ' is not a valid email.');
                }
            }
            
            return $value;
        };
    }
}