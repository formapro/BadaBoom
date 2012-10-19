<?php
namespace BadaBoom\ChainNode\Sender;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

use BadaBoom\ChainNode\AbstractChainNode;
use BadaBoom\Adapter\SenderAdapterInterface;
use BadaBoom\Context;

abstract class AbstractSender extends AbstractChainNode
{
    /**
     * @var \Symfony\Component\Serializer\Serializer
     */
    protected $serializer;

    /**
     * @var SenderAdapterInterface
     */
    protected $adapter;

    /**
     * @var array
     */
    protected $options;

    /**
     * @param \BadaBoom\Adapter\SenderAdapterInterface $adapter
     * @param \Symfony\Component\Serializer\Serializer $serializer
     * @param array $options
     */
    public function __construct(SenderAdapterInterface $adapter, Serializer $serializer, array $options)
    {
        $this->serializer = $serializer;
        $this->adapter = $adapter;
        
        $this->options = $this->getOptionResolver()->resolve($options);
    }

    /**
     * @return OptionsResolver
     */
    protected function getOptionResolver()
    {   
        $resolver = new OptionsResolver();
        $resolver->setRequired(array(
            'format'
        ));
        
        $resolver->setNormalizers(array(
            'format' => $this->getFormatNormalizer(),
        ));
        
        return $resolver;
    }

    /**
     * @param Context $context
     * 
     * @return void
     */
    public function serialize(Context $context)
    {
        return $this->serializer->serialize($context, $this->options['format']);
    }

    /** 
     * @return \Closure
     */
    protected function getFormatNormalizer()
    {
        $serializer = $this->serializer;
        
        return function(Options $options, $value) use ($serializer) {
            if (false == $serializer->supportsEncoding($value)) {
                throw new InvalidOptionsException(sprintf('Given format "%s" is not supported by serializer.', $value));
            }

            return $value;
        };
    }
}