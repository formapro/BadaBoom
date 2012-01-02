<?php

namespace BadaBoom\ChainNode\Sender;

use BadaBoom\ChainNode\AbstractChainNode;
use BadaBoom\DataHolder\DataHolderInterface;
use BadaBoom\Adapter\SenderAdapterInterface;

use Symfony\Component\Serializer\Serializer;

abstract class AbstractSender extends AbstractChainNode
{
    /**
     * @var \Symfony\Component\Serializer\Serializer
     */
    protected $serializer;

    /**
     * @var \BadaBoom\DataHolder\DataHolderInterface
     */
    protected $configuration;

    /**
     * @var \BadaBoom\ChainNode\Sender\AdapterInterface
     */
    protected $adapter;

    /**
     * @param \BadaBoom\Adapter\SenderAdapterInterface $adapter
     * @param \Symfony\Component\Serializer\Serializer $serializer
     * @param \BadaBoom\DataHolder\DataHolderInterface $configuration
     */
    public function __construct(SenderAdapterInterface $adapter, Serializer $serializer, DataHolderInterface $configuration)
    {
        $this->validateFormat($configuration->get('format'), $serializer);

        $this->serializer = $serializer;
        $this->configuration = $configuration;
        $this->adapter = $adapter;
    }

    /**
     *
     * @param DataHolderInterface $data
     * @return void
     */
    public function serialize(DataHolderInterface $data)
    {
        return $this->serializer->serialize($data, $this->configuration->get('format'));
    }

    /**
     * @throws \InvalidArgumentException
     *
     * @param string $format
     * @param \Symfony\Component\Serializer\Serializer $serializer
     * 
     * @return void
     */
    protected function validateFormat($format, Serializer $serializer)
    {
        if(false == $format) {
            throw new \InvalidArgumentException('Mandatory field "format" is missing in the given configuration');
        }

        if (false == $serializer->supportsEncoding($format)) {
            throw new \InvalidArgumentException(sprintf(
                'Given format "%s" is not supported by serializer',
                $format
            ));
        }
    }
}