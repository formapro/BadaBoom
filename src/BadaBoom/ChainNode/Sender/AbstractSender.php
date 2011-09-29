<?php

namespace BadaBoom\ChainNode\Sender;

use BadaBoom\ChainNode\AbstractChainNode;
use BadaBoom\DataHolder\DataHolderInterface;
use BadaBoom\Adapter\SenderAdapterInterface;

use Symfony\Component\Serializer\SerializerInterface;

abstract class AbstractSender extends AbstractChainNode
{
    /**
     * @var \Symfony\Component\Serializer\SerializerInterface
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
     *
     * @param SenderAdapterInterface $adapter
     * @param SerializerInterface $serializer
     * @param array $parameters
     */
    public function __construct(SenderAdapterInterface $adapter, SerializerInterface $serializer, DataHolderInterface $configuration)
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
     * @param \Symfony\Component\Serializer\SerializerInterface $serializer
     * 
     * @return void
     */
    protected function validateFormat($format, SerializerInterface $serializer)
    {
        if(false == $format) {
            throw new \InvalidArgumentException('Mandatory field "format" is missing in the given configuration');
        }

        if (false == $serializer->supportsSerialization($format)) {
            throw new \InvalidArgumentException(sprintf(
                'Given format "%s" is not supported by serializer',
                $format
            ));
        }
    }
}