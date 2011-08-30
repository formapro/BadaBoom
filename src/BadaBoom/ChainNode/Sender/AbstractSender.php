<?php

namespace BadaBoom\ChainNode\Sender;

use BadaBoom\ChainNode\AbstractChainNode;
use BadaBoom\DataHolder\DataHolderInterface;
use BadaBoom\Adapter\AdapterInterface;

use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;

abstract class AbstractSender extends AbstractChainNode
{
    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var string
     */
    protected $format;

    /**
     *
     * @param AdapterInterface $adapter
     * @param SerializerInterface $serializer
     * @param array $parameters
     */
    public function __construct(AdapterInterface $adapter, SerializerInterface $serializer, array $parameters = array())
    {
        $this->serializer = $serializer;
    }

    /**
     *
     * @param \BadaBoom\DataHolder\DataHolderInterface $data
     * @return void
     */
    public function serialize(DataHolderInterface $data)
    {
        $this->serializer->serialize($data, $this->format);
    }

    /**
     *
     * @throws \InvalidArgumentException
     * @param string $format
     * @return Sender
     */
    public function setFormat($format)
    {
        if (false == $this->serializer->supportsSerialization($format)) {
            throw new \InvalidArgumentException('Given format "%s" is not supported by serializer');
        }
        $this->format = $format;

        return $this;
    }
}