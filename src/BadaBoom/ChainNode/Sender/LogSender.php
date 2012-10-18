<?php
namespace BadaBoom\ChainNode\Sender;

use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

use BadaBoom\Adapter\Logger\LoggerAdapterInterface;
use BadaBoom\Context;

class LogSender extends AbstractSender
{
    const INFO     = 10;
    const DEBUG    = 20;
    const NOTICE   = 30;
    const WARNING  = 40;
    const ALERT    = 50;
    const ERROR    = 60;
    const CRITICAL = 70;

    /**
     * @var \BadaBoom\Adapter\Logger\LoggerAdapterInterface
     */
    protected $adapter;

    /**
     * @param \BadaBoom\Adapter\Logger\LoggerAdapterInterface $adapter
     * @param \Symfony\Component\Serializer\SerializerInterface $serializer
     * @param array $options
     */
    public function __construct(LoggerAdapterInterface $adapter, SerializerInterface $serializer, array $options)
    {
        parent::__construct($adapter, $serializer, $options);
    }
    
    protected function getOptionResolver()
    {
        $resolver = parent::getOptionResolver();
        
        $resolver->setDefaults(array(
            'log_level' => self::INFO
        ));
        
        $resolver->setNormalizers(array(
            'log_level' => $this->getLogLevelNormalizer()
        ));
        
        return $resolver;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Context $context)
    {
        $this->adapter->log(
            $this->serialize($context),
            $context->getVar('log_level', $this->options['log_level'])
        );
        
        $this->handleNextNode($context);
    }

    /**
     * @return \Closure
     */
    protected function getLogLevelNormalizer()
    {
        $ro = new \ReflectionObject($this);
        $supportedLevels = $ro->getConstants();

        return function(Options $options, $value) use ($supportedLevels) {
            if (false == in_array($value, $supportedLevels, $strict = true)) {
                throw new InvalidOptionsException(sprintf('Given log_level "%s" is not supported.', $value));
            }

            return $value;
        };
    }
}