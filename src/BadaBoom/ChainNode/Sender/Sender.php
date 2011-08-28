<?php

namespace BadaBoom\ChainNode\Sender;

use BadaBoom\ChainNode\AbstractChainNode;
use BadaBoom\DataHolder\DataHolderInterface;
use BadaBoom\ChainNode\Sender\SenderAdapterInterface;

use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;

class Sender extends AbstractChainNode
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
     * @param SenderAdapterInterface $adapter
     * @param \Symfony\Component\Serializer\SerializerInterface $serializer
     */
    public function __construct(SenderAdapterInterface $adapter, SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     *
     * @param \BadaBoom\DataHolder\DataHolderInterface $data
     * @return void
     */
    public function handle(DataHolderInterface $data)
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