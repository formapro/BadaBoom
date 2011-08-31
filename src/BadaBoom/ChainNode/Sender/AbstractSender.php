<?php

namespace BadaBoom\ChainNode\Sender;

use BadaBoom\ChainNode\AbstractChainNode;
use BadaBoom\DataHolder\DataHolderInterface;
use BadaBoom\Adapter\AdapterInterface;

use Symfony\Component\Serializer\SerializerInterface;

abstract class AbstractSender extends AbstractChainNode
{
    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var DataHolderInterface
     */
    protected $configuration;



    /**
     *
     * @param AdapterInterface $adapter
     * @param SerializerInterface $serializer
     * @param array $parameters
     */
    public function __construct(AdapterInterface $adapter, SerializerInterface $serializer, DataHolderInterface $configuration)
    {
        if (false == $serializer->supportsSerialization($configuration->get('format'))) {
            throw new \InvalidArgumentException(sprintf(
                'Given format "%s" is not supported by serializer',
                $configuration->get('format')
            ));
        }
        $this->serializer = $serializer;
        $this->configuration = $configuration;
    }

    /**
     *
     * @param DataHolderInterface $data
     * @return void
     */
    public function serialize(DataHolderInterface $data)
    {
        $this->serializer->serialize($data, $this->configuration->get('format'));
    }
}